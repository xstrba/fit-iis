/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * Components
 */
import DataTable from "./components/DataTable";
import AssistantsList from "./components/AssistantsList";
import VueSimpleAlert from "vue-simple-alert";
import GroupsForm from "./components/GroupsForm";

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

// ignore ion-icons
Vue.config.ignoredElements = [/^ion-/];

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.use(VueSimpleAlert);

const app = new Vue({
    el: '#app',

    components: {
        DataTable,
        AssistantsList,
        GroupsForm,
    },
});

[...document.getElementsByClassName('form-panel-label')].forEach(node => {
   node.addEventListener('click', (event) => {
       let targetPanel = document.getElementById(node.dataset.target);
       const color = node.dataset.color;
       let labelsParent = document.getElementById(node.dataset.parent);

       [...document.getElementById(targetPanel.dataset.parent).getElementsByClassName('form-panel')].forEach(panel => {
           if (panel.id !== node.dataset.target && panel.dataset.parent === targetPanel.dataset.parent) {
               panel.classList.add('d-none');
           }
       });

       targetPanel.classList.remove('d-none');

       [...labelsParent.getElementsByClassName('form-panel-label')].forEach(label => {
          if (!label.isEqualNode(node)) {
            let nodeColor = label.dataset.color;
            label.classList.remove('btn-' + nodeColor);
            label.classList.add('btn-outline-' + nodeColor);
          }
       });

       node.classList.remove('btn-outline-' + color);
       node.classList.add('btn-' + color);
   });
});

let timeEl = document.getElementById('globalTime');
if (timeEl) {
    const setGlobalTime  = () => {
        const date = new Date();
        timeEl.innerText = date.toLocaleTimeString('cs-CZ', {timeZone: 'Europe/Prague'});
    };

    // set global time
    setGlobalTime();
    setInterval(setGlobalTime, 1000);
}
