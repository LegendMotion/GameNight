export async function initAnalytics() {
  try {
    const res = await fetch('/api/analytics.php');
    if (!res.ok) return;
    const { measurementId, anonymizeIp, respectDNT } = await res.json();
    if (!measurementId) return;
    if (respectDNT && (navigator.doNotTrack === '1' || window.doNotTrack === '1' || navigator.msDoNotTrack === '1')) {
      return;
    }
    const script = document.createElement('script');
    script.async = true;
    script.src = `https://www.googletagmanager.com/gtag/js?id=${measurementId}`;
    document.head.appendChild(script);
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    window.gtag = gtag;
    gtag('js', new Date());
    const config = {};
    if (anonymizeIp) {
      config.anonymize_ip = true;
    }
    gtag('config', measurementId, config);
  } catch (err) {
    console.error('Analytics init failed', err);
  }
}
