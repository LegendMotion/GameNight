let publisherId = '';

export async function initAds() {
  if (window.location.pathname.startsWith('/admin')) return;
  try {
    const res = await fetch('/api/ads.php');
    if (!res.ok) return;
    const { publisherId: id } = await res.json();
    if (!id) return;
    publisherId = id;
    const script = document.createElement('script');
    script.async = true;
    script.src = `https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=${encodeURIComponent(publisherId)}`;
    script.crossOrigin = 'anonymous';
    document.head.appendChild(script);
  } catch (err) {
    console.error('Ads init failed', err);
  }
}

export function renderAdSlot(containerId, type = 'banner', slotId) {
  if (!publisherId) return;
  const container = document.getElementById(containerId);
  if (!container) return;
  const ins = document.createElement('ins');
  ins.className = 'adsbygoogle';
  ins.setAttribute('data-ad-client', publisherId);
  const slot = slotId || container.dataset.adSlot;
  if (slot) {
    ins.setAttribute('data-ad-slot', slot);
  }
  if (type === 'sidebar') {
    ins.style.display = 'block';
    ins.style.width = '300px';
    ins.style.height = '600px';
  } else {
    ins.style.display = 'block';
  }
  container.appendChild(ins);
  (window.adsbygoogle = window.adsbygoogle || []).push({});
}
