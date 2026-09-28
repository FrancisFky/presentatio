const translations = {
  fr: {
    hero_label: 'Ambassade du Congo au Kenya',
    hero_title: 'Bienvenue à l’Ambassade du Congo au Kenya',
    hero_desc: 'Notre mission est de servir la communauté congolaise, soutenir les relations diplomatiques et garantir assistance et sécurité.',
    btn: 'Prendre Rendez-vous',
    nav_home: 'Accueil',
    nav_about: 'À Propos',
    nav_about_congo: 'À propos du Congo',
    nav_about_embassy: 'À propos de l’Ambassade',
    nav_invest_congo: 'Investir au Congo',
    nav_services: 'Services',
    nav_news: 'Actualités',
    nav_appointment: 'Prendre rendez-vous',
    nav_contact: 'Contact',
    appointment_title: 'Prendre rendez-vous',
    appointment_subtitle: 'Demandez un rendez-vous avec l’ambassade pour des services consulaires ou administratifs.',
    appointment_information: 'Informations sur le rendez-vous',
    label_full_name: 'Nom complet',
    label_email: 'Adresse e-mail',
    label_phone: 'Téléphone',
    label_nationality: 'Nationalité',
    label_service: 'Service demandé',
    label_date: 'Date souhaitée',
    label_time: 'Heure souhaitée',
    appointment_submit: 'Prendre rendez-vous',
    option_select_service: 'Sélectionnez un service',
    about_title: 'Ambassade & Mission',
    about_desc: 'Une représentation officielle engagée dans la diplomatie et l’assistance consulaire.',
    ambassador_title: 'Message de l’Ambassade',
    ambassador_desc: 'Une représentation officielle au service de la diplomatie, de la diaspora et des citoyens.',
    services_title: 'Services consulaires',
    services_desc: 'Services dédiés aux citoyens, aux voyageurs et aux partenaires institutionnels.',
    news_title: 'Actualités & annonces',
    news_desc: 'Retrouvez les dernières informations publiées par l’ambassade.',
    holidays_title: 'Jours fériés',
    holidays_desc: 'Les dates officielles à connaître pour les démarches et les visites.',
    contact_title: 'Contact & Rendez-vous',
    contact_desc: 'Envoyez-nous un message pour toute demande d’assistance consulaire.',
    label_name: 'Nom et Prénom',
    label_email: 'Adresse Email',
    label_message: 'Votre Message',
    send: 'Envoyer le message',
    phone_title: 'Téléphone général',
    email_title: 'Adresse Email',
    office_title: 'Horaires de réception',
    footer_note: 'Ceci est le site officiel de la représentation diplomatique du Congo à Nairobi.'
  },
  en: {
    hero_label: 'Congo Embassy in Kenya',
    hero_title: 'Welcome to the Congo Embassy in Kenya',
    hero_desc: 'Our mission is to serve the Congolese community, support diplomatic relations and provide assistance with integrity.',
    btn: 'Book Appointment',
    nav_home: 'Home',
    nav_about: 'About',
    nav_about_congo: 'About Congo',
    nav_about_embassy: 'About the Embassy',
    nav_invest_congo: 'Invest in Congo',
    nav_services: 'Services',
    nav_news: 'News',
    nav_appointment: 'Book an Appointment',
    nav_contact: 'Contact',
    appointment_title: 'Book an Appointment',
    appointment_subtitle: 'Request a meeting with the Embassy for consular or administrative services.',
    appointment_information: 'Appointment Information',
    label_full_name: 'Full Name',
    label_email: 'Email Address',
    label_phone: 'Phone',
    label_nationality: 'Nationality',
    label_service: 'Requested Service',
    label_date: 'Preferred Date',
    label_time: 'Preferred Time',
    appointment_submit: 'Book Appointment',
    option_select_service: 'Select a service',
    about_title: 'Embassy & Mission',
    about_desc: 'An official representation committed to diplomacy and consular assistance.',
    ambassador_title: 'Message from the Embassy',
    ambassador_desc: 'An official representation dedicated to diplomacy, the diaspora and citizens.',
    services_title: 'Consular Services',
    services_desc: 'Services dedicated to citizens, travelers and institutional partners.',
    news_title: 'News & announcements',
    news_desc: 'Find the latest information published by the embassy.',
    holidays_title: 'Public Holidays',
    holidays_desc: 'The official dates to know for procedures and visits.',
    contact_title: 'Contact & Appointment',
    contact_desc: 'Send us a message for any consular assistance request.',
    label_name: 'Full Name',
    label_email: 'Email Address',
    label_message: 'Your Message',
    send: 'Send Message',
    phone_title: 'General Phone',
    email_title: 'Email Address',
    office_title: 'Reception Hours',
    footer_note: 'This is the official website of the Congolese diplomatic representation in Nairobi.'
  }
};

const storageKey = 'embassy-lang';
const languageButtons = {
  fr: document.getElementById('fr-btn'),
  en: document.getElementById('en-btn')
};

function applyTranslations(lang) {
  const translation = translations[lang] || translations.fr;
  document.querySelectorAll('[data-i18n]').forEach((el) => {
    const key = el.getAttribute('data-i18n');
    if (translation[key]) {
      if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
        el.setAttribute('placeholder', translation[key]);
      } else {
        el.textContent = translation[key];
      }
    }
  });

  Object.entries(languageButtons).forEach(([key, button]) => {
    if (button) {
      button.classList.toggle('active', key === lang);
    }
  });

  document.documentElement.lang = lang;
  localStorage.setItem(storageKey, lang);

  document.querySelectorAll('.nav-dropdown-menu a[href$=".php"], .nav-dropdown-menu a[href*=".php?"]').forEach((link) => {
    const url = new URL(link.href, window.location.href);
    url.searchParams.set('lang', lang);
    link.href = url.pathname.split('/').pop() + '?' + url.searchParams.toString();
  });
}

function initLanguage() {
  const serverLang = new URLSearchParams(window.location.search).get('lang');
  if (serverLang === 'fr' || serverLang === 'en') {
    localStorage.setItem(storageKey, serverLang);
    return;
  }
  const savedLang = localStorage.getItem(storageKey) || 'fr';
  applyTranslations(savedLang);

  Object.entries(languageButtons).forEach(([lang, button]) => {
    if (button) {
      button.addEventListener('click', () => applyTranslations(lang));
    }
  });
}

function initMobileMenu() {
  const header = document.querySelector('.header');
  const toggle = document.querySelector('.nav-toggle');
  if (!header || !toggle) return;

  toggle.addEventListener('click', () => {
    header.classList.toggle('mobile-open');
  });

  document.querySelectorAll('.navigation a').forEach((link) => {
    link.addEventListener('click', () => header.classList.remove('mobile-open'));
  });
}

function initAboutDropdown() {
  const dropdown = document.querySelector('.nav-dropdown');
  const toggle = dropdown?.querySelector('.nav-dropdown-toggle');
  if (!dropdown || !toggle) return;
  const close = () => { dropdown.classList.remove('open'); toggle.setAttribute('aria-expanded', 'false'); };
  toggle.addEventListener('click', (event) => {
    event.preventDefault();
    const open = dropdown.classList.toggle('open');
    toggle.setAttribute('aria-expanded', String(open));
  });
  document.addEventListener('click', (event) => { if (!dropdown.contains(event.target)) close(); });
  document.addEventListener('keydown', (event) => { if (event.key === 'Escape') close(); });
}

function initSmoothScroll() {
  document.querySelectorAll('a[href^="#"]').forEach((link) => {
    link.addEventListener('click', (event) => {
      const targetId = link.getAttribute('href').slice(1);
      const target = document.getElementById(targetId);
      if (target) {
        event.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });
}

window.addEventListener('DOMContentLoaded', () => {
  initLanguage();
  initMobileMenu();
  initAboutDropdown();
  initSmoothScroll();
});
