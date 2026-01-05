import requests
r = requests.get('https://digitaldreamscape.site/wp-json/wp/v2/posts?per_page=10&_embed')
posts = r.json()
print(f'Total posts found: {len(posts)}')
for i, p in enumerate(posts, 1):
    print(f'{i}. {p["title"]["rendered"]} - {p["date"][:10]}')
    if 'excerpt' in p and p['excerpt'].get('rendered'):
        excerpt = p['excerpt']['rendered'].replace('<p>', '').replace('</p>', '')[:100]
        print(f'   "{excerpt}..."')