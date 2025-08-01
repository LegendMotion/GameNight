import{S as y}from"./assets/sweetalert2.esm.all-BQIkj5Wb.js";async function h(n="FEST123"){try{const t=await fetch(`/api/collection.php?gamecode=${n}`);if(!t.ok)throw new Error("Klarte ikke å laste spillmodusen.");return await t.json()}catch(t){return console.error(t),null}}function f(n,t={}){const{containerId:e="app",nextBtnId:l="nextBtn",applyBackground:s=!0}=t,a=document.getElementById(e),o=new Set,i=JSON.parse(localStorage.getItem("players")||"[]"),d={challenge:{background:"/backgrounds/challenge.jpg",title:"/titles/challenge.png"},never:{background:"/backgrounds/jegharaldri.jpg",title:"/titles/jegharaldri.png"},spillthetea:{background:"/backgrounds/spillthetea.jpg",title:"/titles/spillthetea.png"},yayornay:{background:"/backgrounds/yayornay.jpg",title:"/titles/yayornay.png"}};function u(){const r=n.challenges.filter(m=>!o.has(m.id));if(r.length===0){a.innerHTML="<h2>Spillet er ferdig 🎉</h2>";return}const c=r[Math.floor(Math.random()*r.length)];o.add(c.id),p(c)}function g(r){return i.length===0?r:r.replace(/{{player}}/gi,()=>{const c=Math.floor(Math.random()*i.length);return i[c]})}function p(r){const c=d[r.type]||d.challenge;s&&(document.body.style.backgroundImage=`url('${c.background}')`),a.innerHTML=`
      <div class="challenge-card">
        <img src="${c.title}" alt="${r.type}" />
        <h3>${g(r.title)}</h3>
        <button id="${l}">Neste</button>
      </div>
    `,document.getElementById(l).addEventListener("click",u)}u()}function k(){const n=document.getElementById("app");n.innerHTML=`
    <h2>Velg spillmodus</h2>
    <input id="gamecodeInput" placeholder="FEST123" />
    <button id="loadBtn">Start spill</button>
  `,document.getElementById("loadBtn").addEventListener("click",async()=>{const t=document.getElementById("gamecodeInput").value.trim(),e=await h(t);e?(localStorage.setItem("activeCollection",JSON.stringify(e)),f(e)):y.fire({icon:"error",title:"Oops... ",text:"Fant ikke spillmodus med kode "+t})})}function v(){const n=document.getElementById("app");n.innerHTML=`
    <h2>Legg til spillere</h2>
    <ul id="playerList"></ul>
    <input id="playerName" placeholder="Spillernavn" />
    <button id="addPlayer">Legg til</button>
    <p id="playerMessage" style="color: red;"></p>
    <button id="continue">Fortsett</button>
  `;const t=document.getElementById("playerList"),e=[],l=document.getElementById("playerMessage");document.getElementById("addPlayer").addEventListener("click",()=>{const a=document.getElementById("playerName"),o=a.value.trim();if(!o){l.textContent="Skriv inn et navn.";return}if(e.includes(o)){l.textContent="Navnet er allerede lagt til.";return}e.push(o),s(),a.value="",l.textContent=""}),document.getElementById("continue").addEventListener("click",()=>{if(e.length<2){l.textContent="Legg til minst to spillere!";return}localStorage.setItem("players",JSON.stringify(e)),k()});function s(){t.innerHTML="",e.forEach((a,o)=>{const i=document.createElement("li");i.textContent=a;const d=document.createElement("button");d.textContent="Slett",d.addEventListener("click",()=>{e.splice(o,1),s(),e.length>=2&&(l.textContent="")}),i.appendChild(d),t.appendChild(i)})}}function E(){if("serviceWorker"in navigator)return navigator.serviceWorker.register("/service-worker.js").then(n=>{console.log("Service worker registered:",n)}).catch(n=>{console.error("Service worker registration failed:",n)})}document.addEventListener("DOMContentLoaded",()=>{v(),E()});
