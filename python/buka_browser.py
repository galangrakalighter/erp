import asyncio
import json
import random
import re
import os
import sys
import urllib.parse
from playwright.async_api import async_playwright
import gspread
from google.oauth2.service_account import Credentials

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
credentials_path = os.path.join(BASE_DIR, 'credentials.json')

def ubah_format_angka(teks_angka):
    if not teks_angka:
        return 0
    teks = teks_angka.strip().upper().replace(",", ".")
    try:
        if "K" in teks:
            return int(float(teks.replace("K", "")) * 1000)
        elif "M" in teks:
            return int(float(teks.replace("M", "")) * 1000000)
        else:
            return int(float(teks))
    except ValueError:
        return 0

def cek_indikator_indonesia(bio, nickname):
    teks_gabungan = (bio + " " + nickname).lower()
    indikator_indo = [
        "id", "indonesia", "jakarta", "bandung", "surabaya", "medan", "bali", 
        "jawa", "jabar", "jateng", "jatim", "sumatera", "sulawesi", "kalimantan",
        "indo", "dm", "endorse", "cp", "hub", "wa", "info", "pemesanan", "order",
        "shopee", "tiktokshop", "+62", "08"
    ]
    for kata in indikator_indo:
        if kata in teks_gabungan:
            return True
    return False

def deteksi_kategori(bio, nickname):
    teks = (bio + " " + nickname).lower()
    kategori = {
        "Beauty & Healthy": ["beauty","healthy","health","vitamin","supplement","wellness","nutrition"],
        "Bodycare": ["bodycare","body care","body lotion","soap","sabun","parfum","perfume","deodorant","handbody"],
        "Fashion": ["fashion","ootd","outfit","style","dress","hijab","baju","sepatu","tas"],
        "Food & Beverages": ["food","kuliner","makan","snack","coffee","kopi","tea","minuman","drink","recipe","resep","masak"],
        "Hair care": ["hair","haircare","hair care","shampoo","conditioner","hair tonic","hair oil","rambut"],
        "Lifestyle": ["daily","vlog","lifestyle","travel","travelling","review","tips"],
        "Mom & Kids": ["mom","mama","mommy","ibu","baby","anak","kids","parenting"],
        "Skincare": ["skincare","skin care","serum","toner","facial wash","cleanser","moisturizer","suncreen","sunscreen","acne","glowing"],
        "Sport & Outdoor": ["gym","fitness","workout","running","cycling","basket","football","badminton","sport","outdoor","hiking"]
    }
    score = {nama: 0 for nama in kategori}
    for nama, keywords in kategori.items():
        for key in keywords:
            if key in teks:
                score[nama] += 1
    hasil = max(score, key=score.get)
    if score[hasil] == 0:
        return "Lifestyle"
    return hasil

def deteksi_domisili(teks):
    kota = [
        "Jakarta", "Bandung", "Bogor", "Bekasi", "Depok", 
        "Tangerang", "Surabaya", "Semarang", "Yogyakarta", 
        "Solo", "Malang", "Medan", "Makassar", "Palembang", "Bali"
    ]
    teks = teks.lower()
    for k in kota:
        if k.lower() in teks:
            return k
    return "-"

def estimasi_gmv(follower, likes=0):
    """
    Mengembalikan estimasi nilai GMV pasti (angka integer) 
    berdasarkan perhitungan estimasi konversi dari likes dan followers.
    """
    # Baseline asumsi: Semakin banyak likes/follower, semakin tinggi volume transaksinya.
    # Kita buat formula estimasi kasar berbasis data engagement.
    
    # Asumsi rata-rata nilai produk (AOV) yang dipromosikan (misal: Rp50.000)
    aov_produk = 50000 
    
    # Estimasi total produk terjual berdasarkan persentase likes yang melakukan checkout (misal 0.5% - 1%)
    # Atau kombinasi bobot follower dan likes
    if likes > 0:
        # Asumsi 0.8% dari total likes berkonversi menjadi pembelian produk
        perkiraan_terjual = int(likes * 0.008)
    else:
        # Jika likes tidak ada, gunakan persentase kecil dari follower
        perkiraan_terjual = int(follower * 0.02)
        
    # Pastikan minimal ada angka wajar jika followers/likes ada
    if perkiraan_terjual < 10 and follower > 1000:
        perkiraan_terjual = int(follower * 0.01)

    # Hitung total GMV pasti
    total_gmv = perkiraan_terjual * aov_produk
    
    # Minimal nilai 0 jika benar-benar kosong
    return max(0, total_gmv)

def hitung_engagement_rate(total_likes, followers):
    """
    Menghitung estimasi Engagement Rate (ER) berdasarkan Total Likes dan Followers.
    Rumus: (Total Likes / Followers) / Jumlah Video (asumsi rata-rata) * 100
    Atau pendekatan persentase interaksi langsung.
    """
    if followers <= 0:
        return 0.0
    
    # Pendekatan estimasi ER profil (misal rasio likes terhadap followers)
    # Anda bisa menyesuaikan faktor pembagi (misal dibagi estimasi jumlah video publik)
    estimasi_er = (total_likes / followers) * 100
    
    # Karena total_likes di profil adalah akumulasi semua video, 
    # biasanya dinormalisasi dengan membagi rata-rata jumlah postingan (misal 30 video terakhir)
    perkiraan_jumlah_video = 30 
    er_per_post = estimasi_er / perkiraan_jumlah_video
    
    # Batasi agar masuk akal (biasanya ER TikTok berkisar antara 0.5% sampai 15%)
    return round(min(er_per_post, 25.0), 2)

async def tutup_popup(page):
    selectors = [
        "button[aria-label='Close']",
        "[data-e2e='modal-close-inner-button']",
        "button:has-text('Not now')",
        "button:has-text('Close')",
        "svg[class*='close']",
    ]
    for selector in selectors:
        try:
            if await page.locator(selector).count() > 0:
                await page.locator(selector).first.click(timeout=1000)
                await asyncio.sleep(1)
                return
        except:
            pass

async def scroll_ke_bawah_otomatis(page):
    try:
        await page.mouse.move(600, 400)
        cards = page.locator("a[href*='/@']")
        count = await cards.count()
        if count > 0:
            last_card = cards.nth(count - 1)
            await last_card.scroll_into_view_if_needed()
            await asyncio.sleep(1)
        await page.evaluate("window.scrollTo(0, document.body.scrollHeight);")
        await asyncio.sleep(1)
        await page.mouse.wheel(0, 2500)
        await asyncio.sleep(2)
    except Exception as e:
        pass

async def main():
    try:
        raw_input = sys.stdin.read()
        payload = json.loads(raw_input) if raw_input else {}
    except Exception as e:
        print(json.dumps({
            "status": "error",
            "message": f"Gagal membaca payload dari STDIN: {str(e)}",
            "data": []
        }))
        sys.exit(1)

    keyword_input = payload.get('keyword', 'skincare')
    TARGET_KANDIDAT = int(payload.get('target', 10))
    MIN_FOLLOWER = int(payload.get('minimal_follower', 5000))
    MAX_FOLLOWER = int(payload.get('maximal_follower', 100000))

    encoded_query = urllib.parse.quote(keyword_input)
    QUERY_SEARCH = f"q={encoded_query}"
    
    scope = [
        "https://www.googleapis.com/auth/spreadsheets",
        "https://www.googleapis.com/auth/drive"
    ]

    try:
        creds = Credentials.from_service_account_file(credentials_path, scopes=scope)
        client = gspread.authorize(creds)
        sheet = client.open("Tes Sheet Tiktok").sheet1

        if not sheet.get_all_values():
            sheet.append_row([
                "Username", "Profile", "Nama", "Follower", "Kategori", "GMV", "Domisili"
            ])
    except Exception as e:
        pass

    data_lolos_filter = []
    username_sudah_dicek = set()
    scroll_count = 0
    max_scroll_limit = 300

    async with async_playwright() as p:
        browser = await p.chromium.launch(
            headless=False,
            args=["--disable-blink-features=AutomationControlled"]
        )
        context = await browser.new_context(
            user_agent="Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36",
            viewport={"width": 1280, "height": 800},
            locale="id-ID",
            timezone_id="Asia/Jakarta"
        )
        
        search_page = await context.new_page()
        profile_page = await context.new_page()

        search_url = f"https://www.tiktok.com/search?{QUERY_SEARCH}"
        
        try:
            await search_page.goto(search_url, timeout=60000)
            await asyncio.sleep(3)
            await search_page.reload(timeout=60000)
        except Exception as e:
            await browser.close()
            print(json.dumps({
                "status": "error",
                "message": f"Gagal memuat halaman pencarian TikTok: {str(e)}",
                "data": []
            }))
            sys.exit(1)

        await asyncio.sleep(5)

        while len(data_lolos_filter) < TARGET_KANDIDAT and scroll_count < max_scroll_limit:
            await search_page.bring_to_front()
            links = await search_page.locator("a").all()
            username_target = None

            for link in links:
                try:
                    href = await link.get_attribute("href")
                    if href and "/@" in href:
                        match = re.search(r'@([a-zA-Z0-9_.-]+)', href)
                        if match:
                            uname = match.group(1)
                            if uname not in username_sudah_dicek:
                                username_target = uname
                                break
                except Exception:
                    continue

            if not username_target:
                scroll_count += 1
                await scroll_ke_bawah_otomatis(search_page)
                await tutup_popup(search_page)
                continue

            username_sudah_dicek.add(username_target)
            profile_url = f"https://www.tiktok.com/@{username_target}"

            try:
                await profile_page.goto(profile_url, timeout=30000)
                await profile_page.wait_for_selector("[data-e2e='user-bio']", timeout=6000)

                nickname = await profile_page.locator("[data-e2e='user-title']").inner_text() if await profile_page.locator("[data-e2e='user-title']").count() > 0 else "N/A"
                followers_str = await profile_page.locator("[data-e2e='followers-count']").inner_text() if await profile_page.locator("[data-e2e='followers-count']").count() > 0 else "0"
                likes = await profile_page.locator("[data-e2e='likes-count']").inner_text() if await profile_page.locator("[data-e2e='likes-count']").count() > 0 else "0"
                bio = await profile_page.locator("[data-e2e='user-bio']").inner_text() if await profile_page.locator("[data-e2e='user-bio']").count() > 0 else "N/A"

                jumlah_follower_angka = ubah_format_angka(followers_str)
                jumlah_likes_angka = ubah_format_angka(likes) # Parsing nilai likes ke angka
                engagement_rate = hitung_engagement_rate(jumlah_likes_angka, jumlah_follower_angka)
                kategori = deteksi_kategori(bio, nickname)
                domisili = deteksi_domisili(nickname + " " + bio)
                
                # Memasukkan follower dan likes ke fungsi estimasi_gmv
                gmv = estimasi_gmv(jumlah_follower_angka, jumlah_likes_angka)

                is_indonesia = cek_indikator_indonesia(bio, nickname)
                is_follower_ok = MIN_FOLLOWER <= jumlah_follower_angka <= MAX_FOLLOWER

                if is_indonesia and is_follower_ok:
                    data_baru = {
                        "Username": username_target,
                        "Profile URL": profile_url,
                        "Nama": nickname,
                        "Followers": jumlah_follower_angka,
                        "ER": engagement_rate,
                        "Total Likes": likes,
                        "Kategori": kategori,
                        "GMV": gmv,
                        "Domisili": domisili
                    }
                    data_lolos_filter.append(data_baru)

                    try:
                        sheet.append_row([
                            username_target, profile_url, nickname, followers_str, kategori, gmv, domisili
                        ])
                    except:
                        pass

            except Exception as e:
                pass

            await asyncio.sleep(random.uniform(0.8, 1.5))

            if len(data_lolos_filter) >= TARGET_KANDIDAT:
                break

        await browser.close()

    response_data = json.dumps({
        "status": "success",
        "message": f"Berhasil mendapatkan {len(data_lolos_filter)} kandidat affiliator.",
        "data": data_lolos_filter
    }, ensure_ascii=False)

    sys.stdout.reconfigure(encoding='utf-8')
    print(response_data)

if __name__ == "__main__":
    asyncio.run(main())