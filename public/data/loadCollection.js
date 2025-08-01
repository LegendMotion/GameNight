export async function loadCollection(gamecode = 'FEST123') {
  try {
    const res = await fetch(`/api/collection.php?gamecode=${gamecode}`);
    if (!res.ok) throw new Error('Klarte ikke å laste spillmodusen.');
    const data = await res.json();
    return data;
  } catch (err) {
    console.error(err);
    return null;
  }
}
