   // Configuração do Firebase
   const firebaseConfig = {
    piKey: "AIzaSyA0ztmepmqW28Lbrv1PU9E8dGnZhE0soWw",
authDomain: "belinho-4a703.firebaseapp.com",
databaseURL: "https://belinho-4a703-default-rtdb.firebaseio.com",
projectId: "belinho-4a703",
storageBucket: "belinho-4a703.appspot.com",
messagingSenderId: "678978718971",
appId: "1:678978718971:web:ba70e7d2e9031ce803745e"
};

firebase.initializeApp(firebaseConfig);

// Referência ao Firebase Storage
const storageRef = firebase.storage().ref();


export {storageRef}