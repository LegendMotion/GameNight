import{S as s}from"./assets/sweetalert2.esm.all-BQIkj5Wb.js";async function u(a="FEST123"){try{const t=await fetch(`/api/collection.php?gamecode=${a}`);if(!t.ok)throw new Error("Klarte ikke å laste spillmodusen.");return await t.json()}catch(t){return console.error(t),null}}function p(a){const t=document.getElementById("app"),e=new Set,l=JSON.parse(localStorage.getItem("players")||"[]");function d(){const n=a.challenges.filter(c=>!e.has(c.id));if(n.length===0){t.innerHTML="<h2>Spillet er ferdig 🎉</h2>";return}const o=n[Math.floor(Math.random()*n.length)];e.add(o.id),r(o)}function i(n){return l.length===0?n:n.replace(/{{player}}/gi,()=>{const o=Math.floor(Math.random()*l.length);return l[o]})}function r(n){t.innerHTML=`
      <div class="challenge-card">
        <h3>${i(n.title)}</h3>
        <button id="nextBtn">Neste</button>
      </div>
    `,document.getElementById("nextBtn").addEventListener("click",d)}d()}function m(){const a=document.getElementById("app");a.innerHTML=`
    <h2>Velg spillmodus</h2>
    <input id="gamecodeInput" placeholder="FEST123" />
    <button id="loadBtn">Start spill</button>
  `,document.getElementById("loadBtn").addEventListener("click",async()=>{const t=document.getElementById("gamecodeInput").value.trim(),e=await u(t);e?(localStorage.setItem("activeCollection",JSON.stringify(e)),p(e)):s.fire({icon:"error",title:"Oops... ",text:"Fant ikke spillmodus med kode "+t})})}function g(){const a=document.getElementById("app");a.innerHTML=`
    <h2>Legg til spillere</h2>
    <ul id="playerList"></ul>
    <input id="playerName" placeholder="Spillernavn" />
    <button id="addPlayer">Legg til</button>
    <p id="playerMessage" style="color: red;"></p>
    <button id="continue">Fortsett</button>
  `;const t=document.getElementById("playerList"),e=[],l=document.getElementById("playerMessage");document.getElementById("addPlayer").addEventListener("click",()=>{const i=document.getElementById("playerName"),r=i.value.trim();if(!r){l.textContent="Skriv inn et navn.";return}if(e.includes(r)){l.textContent="Navnet er allerede lagt til.";return}e.push(r),d(),i.value="",l.textContent=""}),document.getElementById("continue").addEventListener("click",()=>{if(e.length<2){l.textContent="Legg til minst to spillere!";return}localStorage.setItem("players",JSON.stringify(e)),m()});function d(){t.innerHTML="",e.forEach((i,r)=>{const n=document.createElement("li");n.textContent=i;const o=document.createElement("button");o.textContent="Slett",o.addEventListener("click",()=>{e.splice(r,1),d(),e.length>=2&&(l.textContent="")}),n.appendChild(o),t.appendChild(n)})}}document.addEventListener("DOMContentLoaded",()=>{g()});
