# generate_blog.py

import subprocess
import json
from jinja2 import Environment, FileSystemLoader
import os
import shlex
import time
import markdown  # Optional: For Markdown to HTML conversion
import logging
import requests
from requests.auth import HTTPBasicAuth
import configparser

# Configure logging
logging.basicConfig(
    filename='autoblogger.log',
    level=logging.INFO,
    format='%(asctime)s:%(levelname)s:%(message)s'
)

# Load configuration
config = configparser.ConfigParser()
config.read('config.ini')

WORDPRESS_URL = config.get('wordpress', 'url')
WORDPRESS_USERNAME = config.get('wordpress', 'username')
WORDPRESS_PASSWORD = config.get('wordpress', 'password')
DEFAULT_CATEGORIES = json.loads(config.get('wordpress', 'categories', fallback='[]'))
DEFAULT_TAGS = json.loads(config.get('wordpress', 'tags', fallback='[]'))
POST_STATUS = config.get('wordpress', 'status', fallback='draft')  # 'publish' or 'draft'

def run_ollama(prompt):
    """
    Runs the Ollama Mistral model with the given prompt and returns the response.
    """
    try:
        command = f'ollama run mistral:latest "{prompt}"'
        logging.info(f'Running command: {command}')
        args = shlex.split(command)
        result = subprocess.run(
            args,
            stdout=subprocess.PIPE,
            stderr=subprocess.PIPE,
            text=True,
            encoding='utf-8',
            check=True
        )
        response = result.stdout.strip()
        logging.info(f'Ollama response: {response}')
        return response
    except subprocess.CalledProcessError as e:
        logging.error(f"Error running Ollama: {e.stderr.strip()}")
        return ""

def generate_content():
    """
    Generates content for different sections of the blog post using Ollama's Mistral model.
    """
    content = {}

    # Define prompts for each section
    prompts = {
        "post_title": "Generate an engaging blog post title about automating blog posts with AI.",
        "post_subtitle": "Generate a subtitle for the blog post titled 'Automating Blog Posts with AI'.",
        "introduction": "Write an introduction section for a blog post about automating blog posts using AI. The introduction should be engaging and set the stage for the rest of the post.",
        "sections": [
            {
                "title": "Choosing the Right Templating Engine",
                "prompt": "Explain how to choose the right templating engine for an autoblogger, highlighting the benefits of using Python's Jinja2."
            },
            {
                "title": "Integrating AI for Content Generation",
                "prompt": "Describe how to integrate AI models like Mistral into an autoblogger to generate dynamic content."
            },
            {
                "title": "Automating the Deployment Process",
                "prompt": "Provide a detailed explanation on automating the deployment process of the generated blog posts to a web server."
            }
        ],
        "image": {
            "title": "Visual Representation of the Automation Process",
            "prompt": "Describe an image that visually represents the process of automating blog post generation using AI."
        },
        "conclusion": "Write a conclusion for the blog post on automating blog posts with AI. The conclusion should summarize the key points and encourage readers to implement the strategies discussed.",
        "cta": {
            "title": "Stay Updated with the Latest AI Blogging Techniques",
            "content": "Subscribe to our newsletter to receive the latest updates, tutorials, and insights on automating your blogging process with AI.",
            "form_action": "/subscribe"
        }
    }

    # Generate each content section
    content['post_title'] = run_ollama(prompts['post_title']) or "Automating Blog Posts with AI: A Comprehensive Guide"
    content['post_subtitle'] = run_ollama(prompts['post_subtitle']) or "Leveraging Artificial Intelligence to Streamline Your Content Workflow"

    introduction_text = run_ollama(prompts['introduction'])
    content['introduction'] = {
        "title": "Introduction",
        "content": introduction_text or "Welcome to this comprehensive guide on automating your blog posts using AI."
    }

    content['sections'] = []
    for section in prompts['sections']:
        section_title = section['title']
        section_content = run_ollama(section['prompt'])
        if not section_content:
            section_content = f"Content for '{section_title}' goes here."
        content['sections'].append({
            "title": section_title,
            "content": section_content
        })
        time.sleep(1)

    image_description = run_ollama(prompts['image']['prompt'])
    image_url = "https://via.placeholder.com/800x400.png?text=Automation+Process"
    content['image'] = {
        "title": prompts['image']['title'],
        "url": image_url,
        "alt": image_description or "An illustrative diagram showing the flow of automating blog post creation using AI."
    }

    conclusion_text = run_ollama(prompts['conclusion'])
    content['conclusion'] = {
        "title": "Conclusion",
        "content": conclusion_text or "In conclusion, automating your blog posts with AI can revolutionize your content creation process."
    }

    content['cta'] = prompts['cta']
    return content

def render_template(content):
    """
    Renders the blog_template.html with the provided content.
    """
    try:
        base_dir = os.path.dirname(os.path.abspath(__file__))
        env = Environment(loader=FileSystemLoader(base_dir))
        template = env.get_template('blog_template.html')
        logging.info(f"Rendering template with content: {json.dumps(content, indent=2)}")
        rendered_html = template.render(content)
        logging.info("Template rendered successfully.")
        return rendered_html
    except Exception as e:
        logging.error(f"Error rendering template: {e}")
        return ""

def save_output(rendered_html, post_title):
    """
    Saves the rendered HTML to the output directory with a filename based on the post title.
    """
    try:
        base_dir = os.path.dirname(os.path.abspath(__file__))
        output_dir = os.path.join(base_dir, 'output')
        os.makedirs(output_dir, exist_ok=True)
        safe_title = ''.join(c for c in post_title if c.isalnum() or c in (' ', '_')).rstrip()
        safe_title = safe_title.strip().replace(' ', '_').lower()
        max_length = 100
        if len(safe_title) > max_length:
            safe_title = safe_title[:max_length].rstrip('_')
        timestamp = time.strftime("%Y%m%d%H%M%S")
        filename = f"{safe_title}_{timestamp}.html"
        output_path = os.path.join(output_dir, filename)
        with open(output_path, 'w', encoding='utf-8') as f:
            f.write(rendered_html)
        logging.info(f"Blog post generated successfully: {output_path}")
        print(f"Blog post generated successfully: {output_path}")
        return output_path
    except Exception as e:
        logging.error(f"Error saving output file: {e}")
        return ""

def post_to_wordpress(title, content, excerpt, categories, tags, image_url=None):
    """
    Posts the generated blog content to a WordPress site using the REST API.
    """
    try:
        wordpress_url = WORDPRESS_URL
        username = WORDPRESS_USERNAME
        password = WORDPRESS_PASSWORD
        auth = HTTPBasicAuth(username, password)

        def get_or_create_term(endpoint, name):
            term_url = f"{wordpress_url}/{endpoint}"
            params = {'search': name}
            response = requests.get(term_url, params=params, auth=auth)
            if response.status_code == 200:
                terms = response.json()
                for term in terms:
                    if term['name'].lower() == name.lower():
                        return term['id']
                # Term not found, create it
                payload = {'name': name}
                response = requests.post(term_url, json=payload, auth=auth)
                if response.status_code == 201:
                    return response.json()['id']
                else:
                    logging.error(f"Failed to create term '{name}': {response.text}")
            else:
                logging.error(f"Failed to get term '{name}': {response.text}")
            return None

        category_ids = [get_or_create_term('categories', cat) for cat in categories]
        tag_ids = [get_or_create_term('tags', tag) for tag in tags]

        post_data = {
            "title": title,
            "content": content,
            "excerpt": excerpt,
            "status": POST_STATUS,  # 'publish' or 'draft'
            "categories": [cid for cid in category_ids if cid],
            "tags": [tid for tid in tag_ids if tid],
        }

        if image_url:
            # Implement image uploading if necessary
            pass  # Placeholder for image uploading code

        response = requests.post(f"{wordpress_url}/posts", json=post_data, auth=auth)
        if response.status_code == 201:
            logging.info(f"Blog post published to WordPress: {response.json().get('link')}")
            return response.json()
        else:
            logging.error(f"Failed to publish post: {response.text}")
            return None
    except Exception as e:
        logging.error(f"Exception in post_to_wordpress: {e}")
        return None

def generate_blog():
    """
    Orchestrates the blog generation process.
    """
    content = generate_content()
    if not content:
        logging.error("Failed to generate content.")
        return None

    rendered_html = render_template(content)
    if not rendered_html:
        logging.error("Failed to render template.")
        return None

    post_title = content.get('post_title', 'blog_post')
    post_excerpt = content['introduction']['content'][:150]
    post_categories = DEFAULT_CATEGORIES
    post_tags = DEFAULT_TAGS
    feature_image = content['image']['url']

    output_path = save_output(rendered_html, post_title)

    # Optionally post to WordPress
    wordpress_response = post_to_wordpress(
        title=post_title,
        content=rendered_html,
        excerpt=post_excerpt,
        categories=post_categories,
        tags=post_tags,
        image_url=feature_image
    )
    if wordpress_response:
        print(f"Blog post published: {wordpress_response['link']}")
    else:
        print("Failed to publish to WordPress. Check logs for details.")

    return output_path
