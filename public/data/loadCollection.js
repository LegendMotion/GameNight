export async function loadCollection(gamecode = 'FEST123') {
  try {
    // Try to load from a static JSON file first
    const staticRes = await fetch(`/data/collections/${gamecode}.json`);
    if (staticRes.ok) {
      return await staticRes.json();
    }

    // Fall back to the API if the static file is missing
    const apiRes = await fetch(`/api/collection.php?gamecode=${gamecode}`);
    if (!apiRes.ok) throw new Error('Klarte ikke å laste spillmodusen.');
    return await apiRes.json();
  } catch (err) {
    console.error(err);
    return null;
  }
}
