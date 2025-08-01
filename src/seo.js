export function setSEO({ title, description, url, image, type = 'website' } = {}) {
  if (title) {
    document.title = title;
  }
  if (description) {
    setMeta('name', 'description', description);
  }
  if (url) {
    setCanonical(url);
  }
  // Open Graph tags
  if (title) setMeta('property', 'og:title', title);
  if (description) setMeta('property', 'og:description', description);
  if (url) setMeta('property', 'og:url', url);
  setMeta('property', 'og:type', type);
  if (image) setMeta('property', 'og:image', image);
  // Twitter Card tags
  setMeta('name', 'twitter:card', image ? 'summary_large_image' : 'summary');
  if (title) setMeta('name', 'twitter:title', title);
  if (description) setMeta('name', 'twitter:description', description);
  if (image) setMeta('name', 'twitter:image', image);
}

function setMeta(attr, name, content) {
  if (!content) return;
  let tag = document.querySelector(`meta[${attr}="${name}"]`);
  if (!tag) {
    tag = document.createElement('meta');
    tag.setAttribute(attr, name);
    document.head.appendChild(tag);
  }
  tag.setAttribute('content', content);
}

function setCanonical(url) {
  let link = document.querySelector('link[rel="canonical"]');
  if (!link) {
    link = document.createElement('link');
    link.setAttribute('rel', 'canonical');
    document.head.appendChild(link);
  }
  link.setAttribute('href', url);
}
