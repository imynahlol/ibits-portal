// --- State Management ---
        const state = {
            isLoggedIn: true, // Start logged in to show RBAC
            currentUser: {
                name: 'Johahnie Gilbuena',
                fullName: 'Johahnie Ynah Marise Capistrano Gilbuena',
                id: '2024-0001-LQ-0',
                role: 'Vice President',
                program: 'BSIT - 3rd Yr',
                permissions: ['admin_org', 'admin_events', 'admin_forms', 'admin_cbl']
            },
            currentView: 'dashboard'
        };

        // --- DOM Elements ---
        const sidebar = document.getElementById('sidebar');
        const publicNav = document.getElementById('public-nav');
        const authMobileHeader = document.getElementById('auth-mobile-header');
        const footer = document.getElementById('app-footer');
        const adminMenu = document.getElementById('admin-menu');
        const views = document.querySelectorAll('.view-section');
        const navLinks = document.querySelectorAll('.nav-link');

        // --- Core Functions ---
        function initApp() {
            updateUIState();
            navigate(state.isLoggedIn ? 'dashboard' : 'public-home');
        }

        function updateUIState() {
            if (state.isLoggedIn) {
                // Show Auth Shell
                sidebar.classList.remove('hidden');
                sidebar.classList.add('flex');
                publicNav.classList.add('hidden');
                authMobileHeader.classList.remove('hidden');
                footer.classList.add('hidden');

                // RBAC Logic: Show/Hide Admin menu
                if (state.currentUser.role === 'Vice President' || state.currentUser.role === 'President') {
                    adminMenu.style.display = 'block';
                } else {
                    adminMenu.style.display = 'none';
                }
            } else {
                // Show Public Shell
                sidebar.classList.add('hidden');
                sidebar.classList.remove('flex');
                publicNav.classList.remove('hidden');
                authMobileHeader.classList.add('hidden');
                footer.classList.remove('hidden');
            }
        }

        function navigate(viewId) {
            // Hide all views
            views.forEach(v => {
                v.classList.remove('active');
                // Optional: reset animations by forcing reflow
                const animates = v.querySelectorAll('.animate-slide-up, .animate-fade-in');
                animates.forEach(el => {
                    el.style.animation = 'none';
                    el.offsetHeight; /* trigger reflow */
                    el.style.animation = null; 
                });
            });

            // Show target view
            const target = document.getElementById(`view-${viewId}`);
            if (target) {
                target.classList.add('active');
                state.currentView = viewId;
                
                // Scroll to top
                document.getElementById('content-viewport').scrollTop = 0;
            }

            // Update active state on sidebar links
            navLinks.forEach(link => {
                link.classList.remove('bg-primary', 'text-white', 'border-secondary');
                link.classList.add('border-transparent');
                
                // Check if onclick contains the viewId
                if (link.getAttribute('onclick').includes(`'${viewId}'`)) {
                    link.classList.add('bg-primary', 'text-white', 'border-secondary');
                    link.classList.remove('border-transparent');
                }
            });

            // Close mobile sidebar if open
            if (window.innerWidth < 768 && state.isLoggedIn) {
                sidebar.classList.add('hidden');
                sidebar.classList.remove('flex');
            }
        }

        function handleLogin(e) {
            e.preventDefault();
            // Simulate login process with delay for realism
            const btn = e.target.querySelector('button');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> Authenticating...';
            
            setTimeout(() => {
                state.isLoggedIn = true;
                updateUIState();
                navigate('dashboard');
                btn.innerHTML = originalText;
            }, 800);
        }

        function logout() {
            state.isLoggedIn = false;
            updateUIState();
            navigate('public-home');
        }

        function toggleDarkMode() {
            document.documentElement.classList.toggle('dark');
        }

        // --- Custom Modal Logic (Replacing alert/confirm) ---
        const modal = document.getElementById('custom-modal');
        const modalContent = document.getElementById('modal-content');
        let confirmCallback = null;

        function showModal(title, body, isPrimary = true, onConfirm = null) {
            document.getElementById('modal-title').innerText = title;
            document.getElementById('modal-body').innerText = body;
            
            const btn = document.getElementById('modal-confirm-btn');
            if(isPrimary) {
                btn.className = "px-4 py-2 btn-primary rounded-lg text-sm font-medium";
            } else {
                btn.className = "px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700";
            }

            confirmCallback = onConfirm || (() => closeModal());

            modal.classList.remove('opacity-0', 'pointer-events-none');
            modalContent.classList.remove('scale-95');
            modalContent.classList.add('scale-100');
        }

        function closeModal() {
            modal.classList.add('opacity-0', 'pointer-events-none');
            modalContent.classList.add('scale-95');
            modalContent.classList.remove('scale-100');
            confirmCallback = null;
        }

        function confirmModal() {
            if (confirmCallback) confirmCallback();
            closeModal();
            // Show a mock toast/success indicator via console for now
            console.log("Action confirmed.");
        }

        // Initialize application
        window.onload = initApp;

/* ===== iBITS v4 additions (patched onto the existing portal) ===== */
const LOGO='assets/images/ibits-logo.png';
const RM=matchMedia('(prefers-reduced-motion:reduce)').matches;
const $=s=>document.querySelector(s), $$=s=>[...document.querySelectorAll(s)];
const esc=s=>String(s??'').replace(/[&<>"]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
/* AI backend: set endpoint to your server route (POST {messages:[{role,content}]} -> {reply}). Never put API keys here. */
const AI_CONFIG={endpoint:''};
/* Data layer: replace loadPortalData() with a real API call when the backend exists. */
const DATA={
 population:{program:{BSIT:850,DIT:390},year:{'1st Year':380,'2nd Year':330,'3rd Year':300,'4th Year':230},section:{'Section A':320,'Section B':310,'Section C':300,'Section D':310},officers:15},
 fines:[{amt:100,paid:false},{amt:50,paid:false}],
 events:[
  {id:1,title:'Orientation 2024',start:'2024-08-14',end:'2024-08-14',st:'08:00',et:'12:00',loc:'PUP Lopez Gym',cat:'Academic',desc:'Welcome program for new members.',pub:true,hl:true},
  {id:2,title:'IT Week 2024',start:'2024-10-14',end:'2024-10-18',st:'08:00',et:'17:00',loc:'PUP Lopez Campus',cat:'Technology',desc:'Week of contests and talks.',pub:true,hl:true},
  {id:3,title:'IT Week 2025',start:'2025-10-13',end:'2025-10-17',st:'08:00',et:'17:00',loc:'PUP Lopez Campus',cat:'Technology',desc:'Seminars, tournaments and exhibits.',pub:true,hl:true},
  {id:4,title:'Foundation Day 2025',start:'2025-11-20',end:'2025-11-20',st:'09:00',et:'16:00',loc:'Covered Court',cat:'Social',desc:'Celebrating the org founding.',pub:true,hl:true},
  {id:5,title:'Orientation 2026',start:'2026-08-12',end:'2026-08-12',st:'08:00',et:'12:00',loc:'PUP Lopez Gym',cat:'Academic',desc:'Welcome program for new members.',pub:true,hl:true},
  {id:6,title:'Intramurals Code Cup',start:'2026-10-05',end:'2026-10-10',st:'09:00',et:'17:00',loc:'Computer Lab',cat:'Competition',desc:'Coding contest running all week.',pub:true,hl:false},
  {id:7,title:'Tech Talk Thursday',start:'2026-10-08',end:'2026-10-08',st:'13:00',et:'15:00',loc:'Room 204',cat:'Technology',desc:'Guest talk on web careers.',pub:true,hl:false},
  {id:8,title:'IT Week 2026',start:'2026-10-12',end:'2026-10-16',st:'08:00',et:'12:00',loc:'PUP Lopez Campus',cat:'Technology',desc:'Technology week with contests, seminars and exhibits.',pub:true,hl:true},
  {id:9,title:'General Assembly',start:'2026-11-14',end:'2026-11-14',st:'09:00',et:'12:00',loc:'Auditorium',cat:'Org Meeting',desc:'Members only: updates and elections.',pub:false,hl:true},
  {id:10,title:'Hackathon: Build as One',start:'2026-12-05',end:'2026-12-06',st:'08:00',et:'08:00',loc:'Computer Lab',cat:'Competition',desc:'24 hours of building together.',pub:true,hl:true},
  {id:11,title:'Orientation 2027',start:'2027-08-11',end:'2027-08-11',st:'08:00',et:'12:00',loc:'PUP Lopez Gym',cat:'Academic',desc:'Welcome program for new members.',pub:true,hl:true}],
 tickets:[
  {id:1,subject:'Cannot open the portal',cat:'Internet / Portal Issue',status:'Open',desc:'The page stays blank on mobile.',owner:'2024-0001-LQ-0',date:'2026-10-06'},
  {id:2,subject:'Wrong name on my profile',cat:'Account Concern',status:'In Progress',desc:'Please fix my middle name.',owner:'2023-0412-LQ-0',date:'2026-10-05'},
  {id:3,subject:'Need the excuse letter template',cat:'Form Request',status:'Resolved',desc:'Where can I get it?',owner:'2024-0001-LQ-0',date:'2026-09-28'}]};
function loadPortalData(){/* TODO: fetch('/api/...') then Object.assign(DATA, json); renderAll(); */}
const PERMS={'President':['admin_org','admin_events','admin_forms','admin_cbl','manage_tickets'],'Vice President':['admin_org','admin_events','admin_forms','admin_cbl','manage_tickets'],'Secretary':['admin_events','admin_forms','manage_tickets'],'Institutional':['admin_events'],'Treasurer':[],'Assistant Treasurer':[],'Auditor':[],'Assistant Auditor':[],'Adviser':['admin_cbl'],'Member':[]};
const has=p=>state.currentUser.permissions.includes(p);

/* ---- icons (Font Awesome cannot load here, so <i class="fa-..."> becomes inline SVG) ---- */
const P_={house:'M3 11l9-8 9 8M5 10v10h5v-6h4v6h5V10',code:'M8 8l-5 4 5 4M16 8l5 4-5 4M14 5l-4 14',file:'M6 3h8l4 4v14H6zM14 3v4h4M9 12h6M9 16h6',calendar:'M3 6h18v15H3zM3 10h18M8 3v4M16 3v4','calendar-check':'M3 6h18v15H3zM3 10h18M8 3v4M16 3v4M9 15l2 2 4-4',headset:'M4 14v-2a8 8 0 0116 0v2M4 14h3v5H4zM17 14h3v5h-3zM17 19c0 1.500-2 2-5 2',sitemap:'M10 3h4v4h-4zM3 15h4v4H3zM17 15h4v4h-4zM12 7v4M12 11H5v4M12 11h7v4',scale:'M12 3v18M5 21h14M5 7h14M5 7l-3 7a3 3 0 006 0zM19 7l-3 7a3 3 0 006 0z',cog:'M12 9a3 3 0 100 6 3 3 0 000-6M12 2v3M12 19v3M2 12h3M19 12h3M5 5l2 2M17 17l2 2M19 5l-2 2M7 17l-2 2',signout:'M9 4H4v16h5M16 8l4 4-4 4M20 12H9',bars:'M3 6h18M3 12h18M3 18h18',bell:'M6 16v-5a6 6 0 0112 0v5l2 2H4zM10 20a2 2 0 004 0',ticket:'M3 7h18v4a2 2 0 000 2v4H3v-4a2 2 0 000-2zM14 7v10','arrow-right':'M5 12h14M13 6l6 6-6 6','arrow-left':'M19 12H5M11 6l-6 6 6 6',shield:'M12 3l8 3v6c0 5-4 8-8 9-4-1-8-4-8-9V6zM9 12l2 2 4-4',lock:'M6 11h12v10H6zM8 11V7a4 4 0 018 0v4',bullseye:'M12 3a9 9 0 100 18 9 9 0 000-18M12 8a4 4 0 100 8 4 4 0 000-8',eye:'M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12zM12 9a3 3 0 100 6 3 3 0 000-6','eye-slash':'M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12zM12 9a3 3 0 100 6 3 3 0 000-6M4 4l16 16',spinner:'M12 3a9 9 0 019 9',user:'M12 4a4 4 0 100 8 4 4 0 000-8M4 21c0-4 4-6 8-6s8 2 8 6',plus:'M12 5v14M5 12h14',xmark:'M6 6l12 12M18 6L6 18',minus:'M5 12h14','paper-plane':'M3 11l18-8-8 18-2-8z','chevron-down':'M6 9l6 6 6-6','chevron-left':'M15 5l-7 7 7 7','chevron-right':'M9 5l7 7-7 7',pin:'M12 21s-7-6-7-11a7 7 0 0114 0c0 5-7 11-7 11zM12 8a2 2 0 100 4 2 2 0 000-4',clock:'M12 3a9 9 0 100 18 9 9 0 000-18M12 7v5l3 2',image:'M3 5h18v14H3zM3 16l5-5 4 4 3-3 6 6',pen:'M4 20l1-5L16 4l4 4L9 19z',trash:'M4 7h16M9 7V4h6v3M6 7l1 13h10l1-13',check:'M5 12l5 5 9-10',robot:'M5 8h14v11H5zM12 8V4M9 13h.01M15 13h.01M3 13v3M21 13v3',dot:'M12 10a2 2 0 100 4 2 2 0 000-4',download:'M12 4v11M7 11l5 5 5-5M4 20h16',moon:'M20 14A8 8 0 1110 4a7 7 0 0010 10z'};
const FA={'file-pdf':'file','file-word':'file','discord':'dot','facebook':'dot','github':'dot','file-invoice-dollar':'file','file-alt':'file','file-lines':'file','calendar-alt':'calendar','calendar-days':'calendar','ticket-alt':'ticket','sign-out-alt':'signout','right-from-bracket':'signout','gear':'cog','scale-balanced':'scale','shield-halved':'shield','sitemap':'sitemap','xmark':'xmark','times':'xmark','user-circle':'user','users':'user','id-card':'user','location-dot':'pin','map-marker-alt':'pin','paper-plane':'paper-plane'};
function iconize(root=document){root.querySelectorAll('i[class*="fa-"]').forEach(i=>{const fa=[...i.classList].find(c=>/^fa-/.test(c)&&!/^fa-(solid|regular|brands)$/.test(c));const n=fa?fa.slice(3):'';const k=FA[n]||n;const d=P_[k]||P_.dot;const s=document.createElementNS('http://www.w3.org/2000/svg','svg');s.setAttribute('viewBox','0 0 24 24');s.setAttribute('class','ico '+[...i.classList].filter(c=>!/^fa/.test(c)).join(' '));s.innerHTML='<path d="'+d+'"/>';i.replaceWith(s)})}
new MutationObserver(m=>{if(m.some(r=>r.addedNodes.length))iconize()}).observe(document.body,{childList:true,subtree:true});
function fixPlaceholders(){$$('img[src*="placehold.co"]').forEach(im=>{const m=im.src.match(/placehold\.co\/(\d+)x(\d+)\/([0-9a-fA-F]+)\/([0-9a-fA-F]+)\?text=([^&]*)/);if(!m)return;im.src='data:image/svg+xml,'+encodeURIComponent(`<svg xmlns="http://www.w3.org/2000/svg" width="${m[1]}" height="${m[2]}"><rect width="100%" height="100%" fill="#${m[3]}"/><text x="50%" y="54%" text-anchor="middle" font-family="sans-serif" font-weight="700" font-size="${m[1]/3}" fill="#${m[4]}">${decodeURIComponent(m[5])}</text></svg>`)})}

/* ---- binary rain (white background, faint navy digits) ---- */
function rain(){$$('canvas.rain').forEach(c=>{if(!c.offsetParent)return;const w=c.width=c.clientWidth,h=c.height=c.clientHeight,f=18,g=c.getContext('2d');if(!c._h||c._w!==w){c._w=w;c._h=Array.from({length:Math.ceil(w/f)},()=>Math.random()*h/f)}
 g.clearRect(0,0,w,h);g.font=f-4+'px monospace';c._h.forEach((y,i)=>{for(let k=0;k<7;k++){const L=!!c.closest('.wp');g.fillStyle=k==0&&i%9==0?'rgba(250,213,23,'+(L?.45:.5)+')':(L?'rgba(6,33,92,':'rgba(190,205,235,')+((L?.12:.3)-k*.04)+')';g.fillText(((i*7+k+Math.floor(y))%3)?1:0,i*f,(y-k)*f)}if(!RM)c._h[i]=y*f>h+120?-Math.random()*20:y+.35})})}
setInterval(rain,90);

/* ---- 3D tilt ---- */
function tilt(){if(RM||matchMedia('(pointer:coarse)').matches)return;$$('.tilt:not([data-t])').forEach(el=>{el.dataset.t=1;el.addEventListener('mousemove',e=>{const r=el.getBoundingClientRect(),x=(e.clientX-r.left)/r.width-.5,y=(e.clientY-r.top)/r.height-.5;el.style.transform=`perspective(800px) rotateX(${-y*8}deg) rotateY(${x*10}deg)`});el.addEventListener('mouseleave',()=>el.style.transform='')})}

/* ---- dates ---- */
const pd=s=>new Date(s+'T00:00:00'),TODAY=(()=>{const d=new Date();d.setHours(0,0,0,0);return d})();
const ymd=d=>d.getFullYear()+'-'+String(d.getMonth()+1).padStart(2,'0')+'-'+String(d.getDate()).padStart(2,'0');
const MN=['January','February','March','April','May','June','July','August','September','October','November','December'];
const fdate=e=>{const a=pd(e.start),b=pd(e.end||e.start);return a.getTime()==b.getTime()?`${MN[a.getMonth()]} ${a.getDate()}, ${a.getFullYear()}`:`${MN[a.getMonth()]} ${a.getDate()}${a.getMonth()==b.getMonth()?'':' '+MN[b.getMonth()]}–${b.getDate()}, ${b.getFullYear()}`};
const t12=t=>{if(!t)return'';const[h,m]=t.split(':').map(Number);return(h%12||12)+':'+String(m).padStart(2,'0')+' '+(h<12?'AM':'PM')};
function statusOf(e){const s=pd(e.start),f=pd(e.end||e.start);if(f<TODAY)return'Past';if(s>TODAY)return'Future';return f>s?'Ongoing':'Current'}
const matches=(e,f)=>f=='All'||statusOf(e)==f||(f=='Current'&&statusOf(e)=='Ongoing');
function art(e){const h=[...e.title].reduce((a,c)=>a+c.charCodeAt(0),0)%40;const bits=Array.from({length:6},(_,i)=>`<text x="12" y="${28+i*22}" fill="#fad517" fill-opacity=".18" font-family="monospace" font-size="14">${Array.from({length:22},(_,k)=>(k*3+i+h)%3?1:0).join(' ')}</text>`).join('');return 'data:image/svg+xml,'+encodeURIComponent(`<svg xmlns="http://www.w3.org/2000/svg" width="480" height="270"><defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#06215c"/><stop offset="1" stop-color="#0a328c"/></linearGradient></defs><rect width="480" height="270" fill="url(#g)"/>${bits}<circle cx="410" cy="60" r="34" fill="#fad517"/><text x="24" y="236" fill="#fff" font-family="sans-serif" font-weight="700" font-size="24">${esc(e.title.slice(0,26))}</text></svg>`)}
const img=e=>e.img||art(e);

/* ---- population counters ---- */
let POP_SEEN=false;
function countTo(el,to,ms=1500){const from=+el.dataset.v||0;el.dataset.v=to;if(RM){el.textContent=to.toLocaleString();return}const t0=performance.now();(function f(t){const p=Math.min((t-t0)/ms,1),e=1-Math.pow(1-p,3);el.textContent=Math.round(from+(to-from)*e).toLocaleString();if(p<1)requestAnimationFrame(f)})(t0)}
function renderStats(){const p=DATA.population,tot=Object.values(p.program).reduce((a,b)=>a+b,0);const card=(n,l,big)=>`<div class="tilt card3d ${big?'p-6':'p-4'} text-center"><div class="stat-num ${big?'text-4xl':'text-2xl'} font-bold text-primary" data-to="${n}">0</div><div class="text-xs text-textMuted font-medium uppercase tracking-wide mt-1">${l}</div></div>`;
 const grid=(o,c)=>`<div class="grid grid-cols-2 ${c} gap-4">${Object.entries(o).map(([k,v])=>card(v,k)).join('')}</div>`;
 $('#pop-stats').innerHTML=`<div class="grid grid-cols-2 md:grid-cols-4 gap-5">${card(tot,'Total Members',1)}${Object.entries(p.program).map(([k,v])=>card(v,k+' Students',1)).join('')}${card(p.officers,'Active Officers',1)}</div><p class="text-center text-xs font-semibold text-textMuted uppercase tracking-widest">By year level</p>${grid(p.year,'md:grid-cols-4')}<p class="text-center text-xs font-semibold text-textMuted uppercase tracking-widest">By section</p>${grid(p.section,'md:grid-cols-4')}`;
 const run=()=>$$('#pop-stats .stat-num').forEach(el=>countTo(el,+el.dataset.to));if(POP_SEEN)run();else{if(window._io)window._io.disconnect();const io=window._io=new IntersectionObserver(en=>{if(en.some(x=>x.isIntersecting)){POP_SEEN=true;run();io.disconnect()}},{threshold:.25});io.observe($('#pop-stats'))}tilt()}

/* ---- modals ---- */
const openM=id=>{$('#'+id).classList.remove('hidden');$('#'+id).classList.add('flex')},closeM=id=>{$('#'+id).classList.add('hidden');$('#'+id).classList.remove('flex')};
addEventListener('keydown',e=>{if(e.key=='Escape')['m-event','m-form','m-ticket'].forEach(closeM)});

/* ---- calendar ---- */
const CAL={y:TODAY.getFullYear(),m:TODAY.getMonth(),f:'All',sel:ymd(TODAY)};
function renderCal(){
 const ys=[...new Set([...DATA.events.map(e=>pd(e.start).getFullYear()),CAL.y,TODAY.getFullYear()+1])].sort();
 $('#year-tabs').innerHTML=ys.map(y=>`<button onclick="setYear(${y})" class="px-4 py-1.5 rounded-full text-sm font-semibold border ${y==CAL.y?'bg-primary text-white border-primary shadow':'bg-white text-primary border-gray-200 hover:border-primary'}">${y}</button>`).join('');
 $('#cal-filters').innerHTML=['All','Past','Current','Ongoing','Future'].map(f=>`<button onclick="CAL.f='${f}';renderCal()" class="px-4 py-1.5 rounded-lg text-sm border ${CAL.f==f?'bg-secondary text-primary border-secondary font-semibold':'bg-white text-textMuted border-gray-200'}">${f}</button>`).join('');
 $('#cal-title').textContent=MN[CAL.m]+' '+CAL.y;
 const first=new Date(CAL.y,CAL.m,1),start=new Date(CAL.y,CAL.m,1-first.getDay());let h='',n=0;
 for(let i=0;i<42;i++){const d=new Date(start);d.setDate(start.getDate()+i);const k=ymd(d),evs=DATA.events.filter(e=>matches(e,CAL.f)&&k>=e.start&&k<=(e.end||e.start));if(evs.length&&d.getMonth()==CAL.m)n+=0;
  h+=`<div class="cal-cell ${d.getMonth()!=CAL.m?'off':''} ${k==ymd(TODAY)?'today':''} ${k==CAL.sel?'sel':''}" onclick="selDay('${k}')"><div class="text-xs font-semibold text-primary">${d.getDate()}</div>${evs.slice(0,2).map(e=>`<button class="chip ${statusOf(e)} hidden sm:block" onclick="event.stopPropagation();showEvent(${e.id})">${esc(e.title)}</button>`).join('')}${evs.length>2?`<div class="text-[10px] text-textMuted hidden sm:block">+${evs.length-2} more</div>`:''}${evs.length?`<div class="sm:hidden flex gap-0.5 mt-1">${evs.slice(0,3).map(()=>'<span class="w-1.5 h-1.5 rounded-full bg-primary"></span>').join('')}</div>`:''}</div>`}
 $('#cal-grid').innerHTML=h;
 const inM=DATA.events.filter(e=>matches(e,CAL.f)&&pd(e.start).getFullYear()*12+pd(e.start).getMonth()<=CAL.y*12+CAL.m&&pd(e.end||e.start).getFullYear()*12+pd(e.end||e.start).getMonth()>=CAL.y*12+CAL.m).length;
 $('#cal-count').textContent=`${inM} ${CAL.f=='All'?'':CAL.f.toLowerCase()+' '}event${inM==1?'':'s'} in ${MN[CAL.m]} ${CAL.y}`;
 dayList();
 const hl=DATA.events.filter(e=>pd(e.start).getFullYear()==CAL.y&&(e.hl||e.img));$('#hl-year').textContent=CAL.y;
 $('#year-hl').innerHTML=hl.length?hl.map(e=>`<button onclick="showEvent(${e.id})" class="tilt card3d overflow-hidden text-left"><img src="${img(e)}" alt="" class="w-full h-36 object-cover"><div class="p-4"><b class="text-primary text-sm block">${esc(e.title)}</b><span class="text-xs text-textMuted">${fdate(e)}</span></div></button>`).join(''):'<p class="text-textMuted text-sm">No highlights for this year yet.</p>';tilt()}
function dayList(){const d=pd(CAL.sel),L=DATA.events.filter(e=>matches(e,CAL.f)&&CAL.sel>=e.start&&CAL.sel<=(e.end||e.start));$('#day-title').textContent=`${MN[d.getMonth()]} ${d.getDate()}, ${d.getFullYear()}`;
 $('#day-list').innerHTML=L.length?L.map(e=>`<button onclick="showEvent(${e.id})" class="w-full text-left p-3 rounded-xl border border-gray-100 hover:border-primary flex gap-3 items-center"><img src="${img(e)}" alt="" class="w-14 h-14 rounded-lg object-cover"><span><b class="text-sm text-primary block">${esc(e.title)}</b><span class="text-xs text-textMuted">${t12(e.st)} – ${t12(e.et)} · ${statusOf(e)}</span></span></button>`).join(''):'<p class="text-sm text-textMuted">No events on this day.</p>'}
const selDay=k=>{CAL.sel=k;renderCal()},setYear=y=>{CAL.y=y;renderCal();renderDash()},calMove=n=>{CAL.m+=n;if(CAL.m<0){CAL.m=11;CAL.y--}if(CAL.m>11){CAL.m=0;CAL.y++}renderCal()},calToday=()=>{CAL.y=TODAY.getFullYear();CAL.m=TODAY.getMonth();CAL.sel=ymd(TODAY);renderCal()};
function showEvent(id){const e=DATA.events.find(x=>x.id==id);if(!e)return;const adm=has('admin_events');
 $('#m-event-c').innerHTML=`<img src="${img(e)}" alt="" class="w-full h-52 object-cover rounded-t-2xl"><div class="p-6"><span class="text-xs font-bold px-2 py-1 rounded-md bg-secondary/30 text-primary">${statusOf(e)} · ${esc(e.cat)}</span><h3 class="text-2xl font-bold text-primary mt-2 mb-3">${esc(e.title)}</h3><dl class="text-sm space-y-2"><div><dt class="text-textMuted text-xs">Date</dt><dd class="font-medium">${fdate(e)}</dd></div><div><dt class="text-textMuted text-xs">Time</dt><dd class="font-medium">${t12(e.st)} – ${t12(e.et)}</dd></div><div><dt class="text-textMuted text-xs">Location</dt><dd class="font-medium">${esc(e.loc)}</dd></div><div><dt class="text-textMuted text-xs">Description</dt><dd>${esc(e.desc)}</dd></div></dl><div class="flex justify-end gap-2 mt-6">${adm?`<button onclick="openEventForm(${e.id})" class="px-4 py-2 border border-gray-200 rounded-lg text-sm">Edit</button><button onclick="delEvent(${e.id})" class="px-4 py-2 border border-red-200 text-red-600 rounded-lg text-sm">Delete</button>`:''}<button onclick="closeM('m-event')" class="btn-primary px-5 py-2 rounded-lg text-sm">Close</button></div></div>`;openM('m-event')}
function openEventForm(id){const e=DATA.events.find(x=>x.id==id)||{title:'',start:CAL.sel,end:'',st:'08:00',et:'17:00',loc:'',cat:'Academic',desc:'',hl:false};const I='w-full px-3 py-2 border border-gray-200 rounded-lg text-sm';
 $('#m-form-c').innerHTML=`<h3 class="text-lg font-bold text-primary">${id?'Edit':'Add'} Event</h3><input type="hidden" id="f-id" value="${id||''}"><input id="f-title" required placeholder="Event Title" value="${esc(e.title)}" class="${I}"><div class="grid grid-cols-2 gap-3"><label class="text-xs text-textMuted">Start date<input id="f-start" type="date" required value="${e.start}" class="${I}"></label><label class="text-xs text-textMuted">End date<input id="f-end" type="date" value="${e.end||''}" class="${I}"></label><label class="text-xs text-textMuted">Start time<input id="f-st" type="time" value="${e.st}" class="${I}"></label><label class="text-xs text-textMuted">End time<input id="f-et" type="time" value="${e.et}" class="${I}"></label></div><input id="f-loc" placeholder="Location" value="${esc(e.loc)}" class="${I}"><select id="f-cat" class="${I} bg-white">${['Academic','Technology','Competition','Social','Org Meeting','Community'].map(c=>`<option ${c==e.cat?'selected':''}>${c}</option>`).join('')}</select><textarea id="f-desc" rows="3" placeholder="Description" class="${I}">${esc(e.desc)}</textarea><label class="text-xs text-textMuted block">Event image (optional, max 2 MB)<input id="f-img" type="file" accept="image/*" class="${I}"></label><label class="flex items-center gap-2 text-sm"><input id="f-hl" type="checkbox" ${e.hl?'checked':''}> Show in year highlights</label><p class="text-xs text-textMuted">Status (Past, Current, Ongoing, Future) is worked out from the dates.</p><div class="flex justify-end gap-2"><button type="button" onclick="closeM('m-form')" class="px-4 py-2 border border-gray-200 rounded-lg text-sm">Cancel</button><button class="btn-primary px-5 py-2 rounded-lg text-sm">Save Event</button></div>`;closeM('m-event');openM('m-form')}
function saveEvent(ev){ev.preventDefault();if(!has('admin_events'))return;const id=+$('#f-id').value,s=$('#f-start').value,en=$('#f-end').value||s;if(en<s)return showModal('Check the dates','The end date cannot be before the start date.');
 const done=im=>{const o={title:$('#f-title').value.trim(),start:s,end:en,st:$('#f-st').value,et:$('#f-et').value,loc:$('#f-loc').value,cat:$('#f-cat').value,desc:$('#f-desc').value,hl:$('#f-hl').checked,pub:true};if(id){const e=DATA.events.find(x=>x.id==id);Object.assign(e,o);if(im)e.img=im}else DATA.events.push({id:Date.now(),...o,img:im||undefined});closeM('m-form');CAL.y=pd(s).getFullYear();CAL.m=pd(s).getMonth();CAL.sel=s;renderAll()};
 const f=$('#f-img').files[0];if(!f)return done();if(f.size>2e6)return showModal('Image too large','Please choose an image under 2 MB.');const r=new FileReader();r.onload=()=>done(r.result);r.readAsDataURL(f)}
function delEvent(id){if(!has('admin_events'))return;showModal('Delete event?','This removes the event from the calendar.',false,()=>{DATA.events=DATA.events.filter(e=>e.id!=id);closeM('m-event');renderAll()})}

/* ---- dashboard, home strip, public events ---- */
function nextEvent(){return DATA.events.filter(e=>pd(e.start)>TODAY).sort((a,b)=>a.start<b.start?-1:1)[0]}
function renderDash(){const u=state.currentUser;$('#dash-fines').textContent='₱'+DATA.fines.filter(f=>!f.paid).reduce((a,f)=>a+f.amt,0).toFixed(2);
 const n=nextEvent();$('#dash-event').textContent=n?n.title:'No upcoming events';$('#dash-event-date').textContent=n?fdate(n):'';const days=n?Math.ceil((pd(n.start)-TODAY)/864e5):0;$('#dash-event-badge').textContent=!n?'None':days<=7?'This Week':'Upcoming';
 $('#dash-tickets').textContent=DATA.tickets.filter(t=>t.owner==u.id&&t.status!='Resolved').length}
const flipCard=e=>`<div class="flip" tabindex="0"><div class="flip-in"><div class="flip-f card3d"><img src="${img(e)}" alt="" class="w-full h-40 object-cover"><div class="p-4"><b class="text-primary text-sm">${esc(e.title)}</b></div></div><div class="flip-b"><b class="text-secondary">${esc(e.title)}</b><p class="text-sm mt-2">${fdate(e)}</p><p class="text-xs text-blue-200 mt-1">${esc(e.loc)}</p><p class="text-xs mt-3">${esc(e.desc)}</p></div></div></div>`;
function renderPublic(){const pub=DATA.events.filter(e=>e.pub);const up=pub.filter(e=>statusOf(e)!='Past').sort((a,b)=>a.start<b.start?-1:1);const rest=pub.filter(e=>statusOf(e)=='Past').sort((a,b)=>a.start<b.start?1:-1);$('#home-events').innerHTML=[...up,...rest].slice(0,4).map(flipCard).join('');$('#pub-events').innerHTML=[...up,...rest].map(flipCard).join('')}

/* ---- help desk ---- */
const BADGE={'Open':'bg-orange-100 text-orange-700','In Progress':'bg-yellow-100 text-yellow-800','Resolved':'bg-green-100 text-green-700'};
function renderTickets(){const u=state.currentUser,staff=has('manage_tickets'),L=staff?DATA.tickets:DATA.tickets.filter(t=>t.owner==u.id);
 $('#tk-scope').textContent=staff?'Staff view: you can see and update every ticket.':'You can see your own tickets.';
 $('#tk-counts').innerHTML=['Open','In Progress','Resolved'].map(s=>`<div class="card3d p-4 text-center"><div class="text-2xl font-bold text-primary">${L.filter(t=>t.status==s).length}</div><div class="text-xs text-textMuted">${s}</div></div>`).join('');
 $('#tk-list').innerHTML=L.length?L.slice().sort((a,b)=>b.id-a.id).map(t=>`<div class="bg-surface border border-gray-100 rounded-2xl p-4 shadow-sm flex flex-wrap gap-3 justify-between items-start"><div><b class="text-primary">Ticket #${String(t.id).padStart(3,'0')}</b> <span class="text-xs text-textMuted">${esc(t.date)}</span><div class="text-sm font-medium">${esc(t.subject)}</div><div class="text-xs text-textMuted">${esc(t.cat)}</div><p class="text-sm text-textMuted mt-1">${esc(t.desc)}</p></div><div class="text-right">${staff?`<select onchange="setTk(${t.id},this.value)" class="text-xs border border-gray-200 rounded-lg px-2 py-1 bg-white">${Object.keys(BADGE).map(s=>`<option ${s==t.status?'selected':''}>${s}</option>`).join('')}</select>`:`<span class="px-3 py-1 rounded-full text-xs font-bold ${BADGE[t.status]}">${t.status.toUpperCase()}</span>`}</div></div>`).join(''):'<p class="text-textMuted text-sm">No tickets yet. Create one if you need help.</p>'}
const setTk=(id,s)=>{DATA.tickets.find(t=>t.id==id).status=s;renderAll()};
const openTicketForm=()=>openM('m-ticket');
function saveTicket(ev){ev.preventDefault();DATA.tickets.push({id:Math.max(0,...DATA.tickets.map(t=>t.id))+1,subject:$('#t-sub').value.trim(),cat:$('#t-cat').value,status:'Open',desc:$('#t-desc').value.trim(),owner:state.currentUser.id,date:ymd(new Date())});ev.target.reset();closeM('m-ticket');renderAll()}

/* ---- AI chatbot ---- */
const CHAT=[{role:'assistant',content:"Hello! I'm the iBITS Help Desk Assistant. How can I help you today?"}];let chatBusy=false;
const KB=[[/excuse|letter|absen/,'You can access the Excuse Letter template through the Forms Hub. Fill it in, then save or print it.'],[/fine|penalt|pay/,'You can view your personal fines through My Fines. It shows the amount, reason, status and due date, and only you can see your own.'],[/event|gpoa|calendar|schedule/,'Open Events / GPOA to see the calendar. Use the Past, Current, Ongoing and Future filters, or click an event for details.'],[/ticket|concern|complain|help desk/,'Go to Help Desk and press Create New Ticket. You can follow it as Open, In Progress or Resolved.'],[/cbl|by-?law/,'The CBL (Org By-Laws) is in the Forms Hub. Changes need Adviser, President and VP approval.'],[/password|login|account/,'Use Forgot password? on the login page, or create an Account Concern ticket.'],[/^(hi|hello|hey)\b/,'Hi! Ask me about fines, events, forms or tickets.']];
async function askAI(){if(AI_CONFIG.endpoint){const r=await fetch(AI_CONFIG.endpoint,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({messages:CHAT})});if(!r.ok)throw new Error('HTTP '+r.status);const j=await r.json();if(!j.reply)throw new Error('Empty reply');return j.reply}
 await new Promise(r=>setTimeout(r,600));const q=CHAT[CHAT.length-1].content.toLowerCase();const k=KB.find(x=>x[0].test(q));return k?k[1]:"I'm not sure about that one. Please create a ticket in Help Desk and an officer will help you."}
function renderChat(extra=''){const b=m=>`<div class="msg-in flex ${m.role=='user'?'justify-end':''}"><div class="max-w-[85%] px-3 py-2 rounded-2xl text-sm ${m.role=='user'?'bg-primary text-white rounded-br-sm':m.err?'bg-red-50 text-red-700 border border-red-200':'bg-white border border-gray-200 text-textDark rounded-bl-sm'}">${esc(m.content)}</div></div>`;const el=$('#chat-msgs');el.innerHTML=CHAT.map(b).join('')+extra;el.scrollTop=el.scrollHeight}
async function sendChat(e){e.preventDefault();const i=$('#chat-in'),q=i.value.trim();if(!q||chatBusy)return;i.value='';CHAT.push({role:'user',content:q});chatBusy=true;$('#chat-send').disabled=true;renderChat('<div class="text-xs text-textMuted msg-in">Assistant is typing…</div>');
 try{CHAT.push({role:'assistant',content:await askAI()})}catch(err){CHAT.push({role:'assistant',content:'Sorry, I could not reach the assistant. Please try again or create a ticket.',err:true})}chatBusy=false;$('#chat-send').disabled=false;renderChat();i.focus()}
function toggleChat(force){const p=$('#chat-panel'),open=force??p.classList.contains('hidden');p.classList.toggle('hidden',!open);p.classList.toggle('flex',open);if(open){renderChat();$('#chat-in').focus()}}

/* ---- login / register ---- */
function loginMode(m){const T={choice:['SECURE ACCESS','IDENTIFY YOURSELF TO PROCEED.'],member:['MEMBER ACCESS','ENTER YOUR CREDENTIALS TO PROCEED.'],admin:['ADMINISTRATOR','ENTER YOUR CREDENTIALS TO PROCEED.'],register:['REGISTER ACCESS','CREATE YOUR MEMBER ACCOUNT.']};LOGIN_MODE=m;$('#lg-title').textContent=T[m][0];$('#lg-sub').textContent=T[m][1];$$('#view-login [data-panel]').forEach(p=>p.classList.toggle('hidden',!p.dataset.panel.split(' ').includes(m)));$$('#view-login [data-only]').forEach(p=>p.classList.toggle('hidden',p.dataset.only!=m))}
let LOGIN_MODE='choice';
function togglePw(id,b){const i=$('#'+id);i.type=i.type=='password'?'text':'password';b.innerHTML='<i class="fa-solid '+(i.type=='password'?'fa-eye':'fa-eye-slash')+'"></i>'}
const RULES=[[/.{8,}/,'At least 8 characters'],[/[A-Z]/,'At least one uppercase letter (A-Z)'],[/[a-z]/,'At least one lowercase letter (a-z)'],[/\d/,'At least one number (0-9)'],[/[!@#$%^&*]/,'At least one special character (!@#$%^&*)'],[/^\S*$/,'No spaces in between']];
function checkPw(){const v=$('#rg-pw').value;$('#pw-rules').innerHTML=RULES.map(r=>`<li class="${r[0].test(v)?'text-green-600':''}">${r[0].test(v)?'✓':'○'} ${r[1]}</li>`).join('');return RULES.every(r=>r[0].test(v))}
function doLogin(e){e.preventDefault();const btn=$('#lg-btn'),old=btn.innerHTML,id=$('#lg-id').value.trim(),adm=LOGIN_MODE=='admin',role=adm?$('#lg-role').value:'Member';btn.disabled=true;btn.innerHTML='<i class="fa-solid fa-spinner animate-spin"></i> Authenticating...';
 /* Authentication is simulated: no backend exists in this project. Replace with a real call, e.g. fetch('/api/login'). */
 setTimeout(()=>{state.currentUser={name:role+' '+id,fullName:id,id,role,program:adm?'Officer':'Member',permissions:PERMS[role]};state.isLoggedIn=true;$('#lg-pw').value='';btn.disabled=false;btn.innerHTML=old;updateUIState();navigate('dashboard')},900)}
function doRegister(e){e.preventDefault();if(!checkPw())return showModal('Password too weak','Please meet every password rule listed.');showModal('Registration submitted','Your account request was recorded. Real registration needs the backend.',true,()=>loginMode('member'));$('#rg-pw').value=''}

/* ---- wiring (wraps existing functions, does not replace them) ---- */
function afterUI(){const u=state.currentUser,adm=has('admin_org')||has('admin_cbl');adminMenu.style.display=adm?'block':'none';$('#admin-banner').style.display=adm?'flex':'none';$('#auth-user-name').textContent=u.name;$('#auth-user-role').textContent=u.role;$('#dash-greeting-name').textContent=u.name.split(' ')[0];const p=$('#admin-banner p');if(p)p.textContent='You have '+u.role+' privileges. '+(has('admin_org')?'You can manage Org structure and verify CBL updates.':'You can verify CBL updates.');$('#add-event-btn').classList.toggle('hidden',!has('admin_events'));$('#chat-fab').classList.toggle('hidden',!state.isLoggedIn);if(!state.isLoggedIn)toggleChat(false);renderAll()}
const _uis=updateUIState;updateUIState=function(){_uis();afterUI()};
const _nav=navigate;navigate=function(id){if(id==='register'){_nav('login');loginMode('register');checkPw();replay();return}_nav(id);if(id==='login'){loginMode('choice');replay()}if(id=='events')renderCal();if(id=='helpdesk')renderTickets();if(id=='dashboard')renderDash();iconize();tilt()};
function replay(){$$('#view-login .login-in').forEach(el=>{el.style.animation='none';el.offsetHeight;el.style.animation=''})}
function renderAll(){renderStats();renderPublic();renderCal();renderTickets();renderDash();iconize();tilt()}
document.addEventListener('DOMContentLoaded',()=>{['m-event-c','m-form-c','chat-panel'].forEach(i=>$('#'+i).classList.add('wp'));$('#m-ticket form').classList.add('wp');$$('.logo-img').forEach(i=>i.src=LOGO);fixPlaceholders();state.currentUser.permissions=PERMS[state.currentUser.role]||state.currentUser.permissions;loginMode('choice');checkPw();renderAll();loadPortalData()});
