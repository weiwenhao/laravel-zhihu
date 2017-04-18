
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');



/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

require('sweetalert');

require('select2')

// Vue.component('question-modal', require('./components/QuestionModal.vue'));
Vue.component('example', require('./components/Example.vue'));
Vue.component('MyNav', require('./components/MyNav.vue')); //导航条
Vue.component('Question', require('./components/Question.vue'));    //问题头
Vue.component('Answers', require('./components/Question/Answers.vue'));    //答案区