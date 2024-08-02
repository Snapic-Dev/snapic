import { storageRef } from './FirebaseConection';



const uploadButton = document.getElementById('uploadButton');

uploadButton.addEventListener('click', () => {
//   const file = imageUpload.files[0];
//   if (file) {
//     uploadImage(file);
//   } else {
//     alert('Selecione uma imagem para fazer upload.');
//   }
});

//upload
function uploadImage(file) {
  const fileRef = storageRef.child('images/' + file.name); 

  fileRef.put(file)
    .then((snapshot) => {
      snapshot.ref.getDownloadURL()
        .then((url) => {
          console.log('URL de download:', url);
        });


    })
    .catch((error) => {
      console.error('Erro ao fazer upload:', error);
    });
}


const imageUpload = document.getElementById('imageUpload');
imageUpload.addEventListener('change', () => {
  const file = imageUpload.files[0];
  if (file) {
    previewImage(file); 
  }
});

// Função para pré-visualizar a imagem
function previewImage(file) {
    const reader = new FileReader();
    const imagePreview = document.getElementById('imagePreview');
  
    reader.onload = (e) => {
      imagePreview.src = e.target.result; 
      imagePreview.style.display = 'block'; 
    };
  
    reader.readAsDataURL(file); 
  }


