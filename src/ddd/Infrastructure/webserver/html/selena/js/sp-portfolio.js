// <sp-portfolio src="portfolio.json">: el organismo raíz.
// Lee los datos (un JSON) y monta la página con el resto de piezas.
// Para cambiar el contenido del portfolio se edita SOLO el JSON.
import './sp-hero.js';
import './sp-nav.js';
import './sp-section.js';
import './sp-contact.js';
import './sp-lightbox.js';

class SpPortfolio extends HTMLElement {
  async connectedCallback() {
    const res = await fetch(this.getAttribute('src'), { cache: 'no-cache' });
    const data = await res.json();
    // tiles.json lo genera un script: las piezas sueltas de cada composición para el móvil.
    const tiles = await fetch('/html/selena/tiles.json').then((r) => r.json()).catch(() => ({}));
    for (const s of data.sections) for (const p of s.projects) p.tiles = tiles[p.image] || [];

    this.innerHTML = '';
    const hero = document.createElement('sp-hero');
    hero.data = data.profile;

    const nav = document.createElement('sp-nav');
    nav.data = data.sections;

    const main = document.createElement('main');
    for (const s of data.sections) {
      const section = document.createElement('sp-section');
      section.id = `s${s.num}`;
      section.data = s;
      main.append(section);
    }

    const contact = document.createElement('sp-contact');
    contact.id = 'contacto';
    contact.data = data.profile;

    const lightbox = document.createElement('sp-lightbox');

    this.append(hero, nav, main, contact, lightbox);

    // Las piezas piden abrir imagen o vídeo con un evento; el lightbox lo atiende.
    this.addEventListener('sp-open', (e) => lightbox.open(e.detail));

    // El contenido se pinta tras cargar: si la URL trae #seccion, ir a ella.
    if (location.hash) requestAnimationFrame(() => document.querySelector(location.hash)?.scrollIntoView());

    // La barra de navegación aparece al dejar atrás la portada.
    new IntersectionObserver(([entry]) => {
      nav.toggleAttribute('visible', !entry.isIntersecting);
    }, { threshold: 0.05 }).observe(hero);
  }
}

customElements.define('sp-portfolio', SpPortfolio);
