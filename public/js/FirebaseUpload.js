const firebaseConfig = {
  piKey: "AIzaSyA0ztmepmqW28Lbrv1PU9E8dGnZhE0soWw",
  authDomain: "belinho-4a703.firebaseapp.com",
  databaseURL: "https://belinho-4a703-default-rtdb.firebaseio.com",
  projectId: "belinho-4a703",
  storageBucket: "belinho-4a703.appspot.com",
  messagingSenderId: "678978718971",
  appId: "1:678978718971:web:ba70e7d2e9031ce803745e",
};

firebase.initializeApp(firebaseConfig);

// Referência ao Firebase Storage
const storage = firebase.storage();

export const UploadImage = async (file) => {
  try {
    const storageRef = ref(storage, `images/${file[0].name}`);
    const uploadTask = uploadBytesResumable(storageRef, file[0]);

    return new Promise((resolve, reject) => {
      uploadTask.on(
        "state_changed",
        (snapshot) => {
          const progress =
            (snapshot.bytesTransferred / snapshot.totalBytes) * 100;
        },
        (error) => {
          reject(new Error(error.message));
        },
        async () => {
          try {
            const url = await getDownloadURL(uploadTask.snapshot.ref);
            resolve(url);
          } catch (error) {
            reject(new Error(error.message));
          }
        }
      );
    });
  } catch (error) {
    throw new Error(error.message);
  }
};
