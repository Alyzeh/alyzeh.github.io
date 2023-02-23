const citySkate = {

  navMenu : document.getElementById('primaryNavigation'),
  navMenuToggler : document.getElementById('navToggle'),

  init: function() {

    this.navMenuToggler.addEventListener('click', this.toggleMenu, false);

  },

  toggleMenu : function() {

    let menuState = this.getAttribute('aria-expanded');

    if (menuState == 'false') {

      citySkate.navMenu.className = 'show';
      this.setAttribute('aria-expanded', 'true');

    }

    else {

      citySkate.navMenu.className = '';
      this.setAttribute('aria-expanded', 'false');

    }

  }

}

citySkate.init();