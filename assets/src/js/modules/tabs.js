const navTabs = document.querySelectorAll('ul.infobip-omnichannel-nav > li a');

const queryString   = window.location.search;
const urlParams     = new URLSearchParams(queryString);
const activeSection = urlParams.get('section');

navTabs.forEach(navTab => {
  navTab.addEventListener('click', (e) => changeTab(e.target, e));
});

if (activeSection) {
  var active = document.getElementById('nav-'+activeSection);
  if (active) {
    changeTab(active);
  }
}

function changeTab(navTab, event = null) {
  if ( event ) {
    event.preventDefault();
  }

  navTabs.forEach(tab => {
    tab.classList.remove('active');
  });

  navTab.classList.add('active');

  var sections = document.querySelectorAll('section');
  sections.forEach(section => {
    section.classList.remove('active');
  });

  let navTabHref    = new URLSearchParams(navTab.getAttribute('href'));
  let navTabSection = navTabHref.get('section');

  document.getElementById(navTabSection).classList.add('active');

  const url = new URL(window.location.href);
  url.searchParams.set('section', navTabSection);
  window.history.replaceState(null, '', url.toString());
}
