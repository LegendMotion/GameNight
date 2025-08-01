async function s(l = "FEST123") {
  try {
    const e = await fetch(`/api/collection.php?gamecode=${l}`);
    if (!e.ok) throw new Error("Klarte ikke å laste spillmodusen.");
    return await e.json();
  } catch (e) {
    return console.error(e), null;
  }
}
function u(l) {
  const e = document.getElementById("app"), t = /* @__PURE__ */ new Set(), r = JSON.parse(localStorage.getItem("players") || "[]");
  function o() {
    const n = l.challenges.filter((c) => !t.has(c.id));
    if (n.length === 0) {
      e.innerHTML = "<h2>Spillet er ferdig 🎉</h2>";
      return;
    }
    const i = n[Math.floor(Math.random() * n.length)];
    t.add(i.id), d(i);
  }
  function a(n) {
    return r.length === 0 ? n : n.replace(/{{player}}/gi, () => {
      const i = Math.floor(Math.random() * r.length);
      return r[i];
    });
  }
  function d(n) {
    e.innerHTML = `
      <div class="challenge-card">
        <h3>${a(n.title)}</h3>
        <button id="nextBtn">Neste</button>
      </div>
    `, document.getElementById("nextBtn").addEventListener("click", o);
  }
  o();
}
function p() {
  const l = document.getElementById("app");
  l.innerHTML = `
    <h2>Velg spillmodus</h2>
    <input id="gamecodeInput" placeholder="FEST123" />
    <button id="loadBtn">Start spill</button>
  `, document.getElementById("loadBtn").addEventListener("click", async () => {
    const e = document.getElementById("gamecodeInput").value.trim(), t = await s(e);
    t ? (localStorage.setItem("activeCollection", JSON.stringify(t)), u(t)) : alert("Fant ikke spillmodus med kode " + e);
  });
}
function m() {
  const l = document.getElementById("app");
  l.innerHTML = `
    <h2>Legg til spillere</h2>
    <ul id="playerList"></ul>
    <input id="playerName" placeholder="Spillernavn" />
    <button id="addPlayer">Legg til</button>
    <p id="playerMessage" style="color: red;"></p>
    <button id="continue">Fortsett</button>
  `;
  const e = document.getElementById("playerList"), t = [], n = document.getElementById("playerMessage");
  document.getElementById("addPlayer").addEventListener("click", () => {
    const o = document.getElementById("playerName"), a = o.value.trim();
    if (!a) {
      n.textContent = "Skriv inn et navn.";
      return;
    }
    if (t.includes(a)) {
      n.textContent = "Navnet er allerede lagt til.";
      return;
    }
    t.push(a), r(), o.value = "", n.textContent = "";
  }), document.getElementById("continue").addEventListener("click", () => {
    if (t.length < 2) {
      n.textContent = "Legg til minst to spillere!";
      return;
    }
    localStorage.setItem("players", JSON.stringify(t)), p();
  });
  function r() {
    e.innerHTML = "", t.forEach((o, i) => {
      const a = document.createElement("li");
      a.textContent = o;
      const c = document.createElement("button");
      c.textContent = "Slett";
      c.addEventListener("click", () => {
        t.splice(i, 1), r(), t.length >= 2 && (n.textContent = "");
      });
      a.appendChild(c), e.appendChild(a);
    });
  }
}
document.addEventListener("DOMContentLoaded", () => {
  m();
});
