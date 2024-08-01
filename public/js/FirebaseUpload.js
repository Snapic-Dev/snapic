import { initializeApp } from "firebase/app";

import { getStorage } from "firebase/storage";
const firebaseConfig = {

    apiKey: "AIzaSyDzndhn4XnMdpgg6LMrGo0DLRpVC0J6vUk",
    authDomain: "belinho-2f5e3.firebaseapp.com",
    projectId: "belinho-2f5e3",
    storageBucket: "belinho-2f5e3.appspot.com",
    messagingSenderId: "788324525556",
    appId: "1:788324525556:web:65f732cb8c64420fc605a1"

};



// Initialize Firebase

const app = initializeApp(firebaseConfig);
const storage = getStorage(app);
export {app, storage}