import Vue from 'vue'
import VueRouter from 'vue-router'
import Home from '../views/Home.vue'
import About from '../views/About.vue'
import CreateMessage from '../views/CreateMessage.vue'
import Messages from '../views/Messages.vue'
import MessagesApi from '../views/MessagesApi.vue'
import Debug from '../views/DebugPage.vue'

Vue.use(VueRouter)

export function createRouter(){
  const originalPush = VueRouter.prototype.push
   VueRouter.prototype.push = function push(location) {
    return originalPush.call(this, location).catch(err => err)
  }
  return new VueRouter({
    mode: 'history',
    base: process.env.BASE_URL,
    routes: [
      {
        path: '/',
        name: 'index',
        alias: ['/message'],
        component: Home
      },
      {
        path: '/message/:to',
        name: 'Message',
        props: true,
        component: Messages
      },
      {
        path: '/about',
        name: 'about',
        component: About
      },
      {
        path: '/create',
        name: 'create',
        component: CreateMessage
      },
      {
        path: '/debug',
        component: Debug
      },
      {
        path: '/api2/msg/:msgId',
        name: 'MessageApi',
        props: true,
        component: MessagesApi
      }
    ]      
  });
}
