import requests
r = requests.get('https://digitaldreamscape.site/wp-json/wp/v2/posts?per_page=5')
posts = r.json()
for p in posts:
    print(f'{p["title"]["rendered"]} - {p["date"][:10]}')