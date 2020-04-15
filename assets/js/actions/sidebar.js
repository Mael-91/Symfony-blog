import dynamics from "dynamics.js"

const contentSidebarLeft = document.querySelector('.nav-left');
const sidebarContent = document.querySelector('.sidebar-content');
const btn = document.querySelector('.hamburger-btn');

sidebarContent.innerHTML = contentSidebarLeft.innerHTML;
const links = sidebarContent.querySelectorAll('a');

links.forEach((link) => {
    link.classList.remove('borderB-lb-active');
    link.classList.remove('borderB-lb-hover');
});

// Sidebar left (mobile)
const sidebarA = document.querySelectorAll('.sidebar-content a');
const sidebarSpan = document.querySelectorAll('.sidebar-content span');
const sidebarSpanBody = document.querySelectorAll('.sidebar-body span');
const sidebarABody = document.querySelectorAll('.sidebar-body a');
let sidebar = false;
let optionsLeft = {
    type: dynamics.easeOut,
    duration: 450,
    friction: 450
};

// Sidebar left (mobile)
btn.addEventListener('click', function (e) {
    e.stopPropagation();
    e.preventDefault();
    document.body.classList.add('has-sidebar');
    sidebarA.forEach(function (link, i) {
        dynamics.animate(link, {
            translateX: 0,
        }, Object.assign({}, optionsLeft, {delay: 50 * i}))
    });
    sidebarSpan.forEach(function (link, i) {
        dynamics.animate(link, {
            translateX: 0,
        }, Object.assign({}, optionsLeft, {delay: 50 * i}))
    });
    sidebarSpanBody.forEach(function (link, i) {
        dynamics.animate(link, {
            translateX: 0,
        }, Object.assign({}, optionsLeft, {delay: 50 * i}))
    });
    sidebarABody.forEach(function (link, i) {
        dynamics.animate(link, {
            translateX: 0,
        }, Object.assign({}, optionsLeft, {delay: 50 * i}))
    });
    sidebar = true;
});

// Sidebar left (mobile)
document.body.addEventListener('click', function (e) {
    if (sidebar) {
        document.body.classList.remove('has-sidebar');
        sidebarA.forEach(function (link, i) {
            dynamics.animate(link, {
                translateX: -190,
            })
        });
        sidebarSpan.forEach(function (link, i) {
            dynamics.animate(link, {
                translateX: -190,
            })
        });
        sidebarSpanBody.forEach(function (link, i) {
            dynamics.animate(link, {
                translateX: -190,
            })
        });
        sidebarABody.forEach(function (link, i) {
            dynamics.animate(link, {
                translateX: -190,
            })
        });
        sidebar = false;
    }
});

// Sidebar right (profil)
const button = document.getElementById('btn-sidebar-profil');
const sidebarProfilA = document.querySelectorAll('.sidebar-p-content a')
const sidebarProfilSpan = document.querySelectorAll('.sidebar-p-content span')
const sidebarProfilSpanBody = document.querySelectorAll('.sidebar-p-body span')
let profilSidebar = false;
let options = {
    type: dynamics.easeOut,
    duration: 450,
    friction: 450
};

// Sidebar right (profil)
button.addEventListener('click', function (e) {
    e.stopPropagation();
    e.preventDefault();
    document.body.classList.add('has-profil-sidebar');
    sidebarProfilA.forEach(function (link, i) {
        dynamics.animate(link, {
            translateX: 0
        }, Object.assign({}, options, {delay: 50 * i}))
    });
    sidebarProfilSpan.forEach(function (link, i) {
        dynamics.animate(link, {
            translateX: 0
        }, Object.assign({}, options, {delay: 50 * i}))
    });
    sidebarProfilSpanBody.forEach(function (link, i) {
        dynamics.animate(link, {
            translateX: 0
        }, Object.assign({}, options, {delay: 50 * i}))
    });
    profilSidebar = true
});

// Sidebar right (profil)
document.body.addEventListener('click', function () {
    if (profilSidebar) {
        document.body.classList.remove('has-profil-sidebar');
        sidebarProfilA.forEach(function (link) {
            dynamics.animate(link, {
                translateX: 190
            }, options)
        });
        sidebarProfilSpan.forEach(function (link) {
            dynamics.animate(link, {
                translateX: 190
            }, options)
        });
        sidebarProfilSpanBody.forEach(function (link) {
            dynamics.animate(link, {
                translateX: 190
            }, options)
        });
        profilSidebar = false
    }
});