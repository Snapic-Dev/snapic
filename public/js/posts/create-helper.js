/**
 * Post create (helper) component
 */
"use strict";
/* global app, Post, user, FileUpload, updateButtonState, launchToast, trans, redirect, trans_choice, mediaSettings, passesMinMaxPPVContentCreationLimits, getWebsiteFormattedAmount */

$(function () {
  $("#post-price").keypress(function (e) {
    if (e.which === 13) {
      PostCreate.savePostPrice();
    }
  });
});

var PostCreate = {
  // Paid post price
  postPrice: 0,
  requires_subscription: false,
  isSavingRedirect: false,
  postNotifications: false,
  postReleaseDate: null,
  postExpireDate: null,

  /**
   * Toggles post notification state
   */
  togglePostNotifications: function () {
    let buttonIcon = "";
    if (PostCreate.postNotifications === true) {
      PostCreate.postNotifications = false;
      buttonIcon = `<div class="d-flex justify-content-center align-items-center mr-1"><ion-icon class="icon-medium" name="notifications-off-outline"></ion-icon></div>`;
    } else {
      buttonIcon = `<div class="d-flex justify-content-center align-items-center mr-1"><ion-icon class="icon-medium" name="notifications-outline"></ion-icon></div>`;
      PostCreate.postNotifications = true;
    }
    $(".post-notification-icon").html(buttonIcon);
  },

  /**
   * Shows up the post price setter dialog
   */
  showSetPricePostDialog: function () {
    $("#post-set-price-dialog").modal("show");
  },

  /**
   * Saves the post price into the state
   */
  savePostPrice: function () {
    PostCreate.postPrice = $("#post-price").val();
    let hasError = false;
    if (!passesMinMaxPPPostLimits(PostCreate.postPrice)) {
      hasError = "min";
    }
    if (PostCreate.postExpireDate !== null) {
      hasError = "ppv";
    }
    if (hasError) {
      $(".post-price-error").addClass("d-none");
      $("#post-set-price-dialog ." + hasError + "-error").removeClass("d-none");
      $("#post-price").addClass("is-invalid");
      return false;
    }
    $(".post-price-label").html("(" + getWebsiteFormattedAmount(PostCreate.postPrice) + ")");
    $("#post-set-price-dialog").modal("hide");
    $("#post-price").removeClass("is-invalid");
  },

  saveRequireSubscription: function () {
    PostCreate.requires_subscription = $(".requires_subscription_post").prop("checked");
  },

  /**
   * Clears up post price
   */
  clearPostPrice: function () {
    PostCreate.postPrice = 0;
    $("#post-price").val(0);
    $(".post-price-label").html("");
    $("#post-set-price-dialog").modal("hide");
    $("#post-price").removeClass("is-invalid");
  },

  /**
   * Initiates the post draft data, if available
   * @param data
   * @param type
   */
  initPostDraft: function (data, type = "draft") {
    Post.initialDraftData = Post.draftData;
    if (data) {
      Post.draftData = data;
      if (type === "draft") {
        FileUpload.attachaments = data.attachments;
      } else {
        data.attachments.map(function (item) {
          FileUpload.attachaments.push({ attachmentID: item.id, path: item.path, type: item.attachmentType, thumbnail: item.thumbnail });
        });
      }
      $("#dropzone-uploader").val(Post.draftData.text);
    }
  },

  /**
   * Clears up post draft data
   */
  clearDraft: function () {
    // Clearing attachments from the backend
    Post.draftData.attachments.map(function (value) {
      FileUpload.removeAttachment(value.attachmentID);
    });
    // Removing previews
    $(".dropzone-previews .dz-preview ").each(function (index, item) {
      $(item).remove();
    });
    // Clearing Fileupload class attachments
    FileUpload.attachaments = [];
    // Clearing up the local storage object
    PostCreate.clearDraftData();
    // Clearing up the text area value
  },

  /**
   * Saves post draft data
   */
  saveDraftData: function () {
    Post.draftData.attachments = FileUpload.attachaments;
    Post.draftData.text = $("#dropzone-uploader").val();
    localStorage.setItem("draftData", JSON.stringify(Post.draftData));
  },

  /**
   * Clears up draft data
   * @param callback
   */
  clearDraftData: function (callback = null) {
    localStorage.removeItem("draftData");
    Post.draftData = Post.initialDraftData;
    if (callback !== null) {
      callback;
    }
    $("#dropzone-uploader").val(Post.draftData.text);
  },

  /**
   * Populates create/edit post form with draft data
   * @returns {boolean|any}
   */
  populateDraftData: function () {
    const draftData = localStorage.getItem("draftData");
    if (draftData) {
      return JSON.parse(draftData);
    } else {
      return false;
    }
  },

  /**
   * Save new / update post
   * @param type
   * @param postID
   */
  generateRandomId: function () {
    return Math.random().toString(36).substr(2, 26);
  },
  uploadVideo: async function () {
    const { initializeApp } = await import("https://www.gstatic.com/firebasejs/9.0.0/firebase-app.js");
    const { getStorage, ref, uploadBytesResumable, getDownloadURL } = await import("https://www.gstatic.com/firebasejs/9.0.0/firebase-storage.js");

    const videoInput = $("#upload_video_post")[0];
    const videoFile = videoInput.files[0];

    const firebaseConfig = {
      apiKey: "AIzaSyCqyMEep9-140R2oPYY7AcBw3ycryQ-TWk",
      authDomain: "snapic-e326c.firebaseapp.com",
      projectId: "snapic-e326c",
      storageBucket: "snapic-e326c.appspot.com",
      messagingSenderId: "173447004036",
      appId: "1:173447004036:web:e06cfe657f50c5572e27d1",
      measurementId: "G-NVV0XMVCBR",
    };

    const app = initializeApp(firebaseConfig);
    const storage = getStorage(app);

    // Função para capturar o frame do vídeo
    const captureFrame = (file) => {
      return new Promise((resolve, reject) => {
        const video = document.createElement("video");
        const canvas = document.createElement("canvas");
        const ctx = canvas.getContext("2d");

        video.preload = "metadata";
        video.src = URL.createObjectURL(file);

        video.onloadeddata = () => {
          canvas.width = video.videoWidth;
          canvas.height = video.videoHeight;
          ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
          const base64Image = canvas.toDataURL("image/jpeg");
          resolve(base64Image);
        };

        video.onerror = (error) => {
          reject(error);
        };
      });
    };

    // Função para upload do arquivo
    const uploadFile = async (file, path) => {
      const storageRef = ref(storage, path);
      const uploadTask = uploadBytesResumable(storageRef, file);
      return new Promise((resolve, reject) => {
        uploadTask.on(
          "state_changed",
          (snapshot) => {
            const progress = (snapshot.bytesTransferred / snapshot.totalBytes) * 100;
            $("#percentage-upload-video").text(Math.floor(progress) + "%");
          },
          (error) => reject(error),
          async () => {
            const downloadURL = await getDownloadURL(uploadTask.snapshot.ref);
            resolve(downloadURL);
          }
        );
      });
    };

    try {
      const video = await uploadFile(videoFile, `uploads/videos/${videoFile.name}`);

      const file = "teste";
      return { video, file };
    } catch (error) {
      return null;
    }
  },

  save: async function (type = "create", postID = false, forceSave = false) {
    PostCreate.saveRequireSubscription();
    if ((FileUpload.isLoading === true || FileUpload.isTranscodingVideo === true) && !forceSave) {
      let dialogMessage = "";

      if (FileUpload.isLoading) {
        dialogMessage = `${trans("Some attachments are still being uploaded.")} ${trans("Are you sure you want to continue?")}`;
      }

      if (FileUpload.isTranscodingVideo) {
        dialogMessage = `${trans("A video is currently being converted.")} ${trans("Are you sure you want to continue without it?")}`;
      }

      $("#confirm-post-save .modal-body p").html(dialogMessage);
      $(".confirm-post-save")
        .unbind("click")
        .on("click", function () {
          PostCreate.save(type, postID, true);
        });

      $("#confirm-post-save").modal("show");
      return false;
    }
    $(".spinner-post-create").removeClass("d-none");
    $(".post-create-button").addClass("disabled", true);
    $(".post-price-button").addClass("d-none");
    $(".file-upload-button").addClass("d-none");
    $(".requires_subscription_post_container").addClass("d-none");
    $(".requires_subscription_post_container").removeClass("d-flex");
    $(".draft-clear-button").addClass("d-none");
    $("#remove_long_video").addClass("d-none");
    $(".post-notification-button ").addClass("d-none");

    PostCreate.savePostScheduleSettings();

    let upload_video;

    if ($("#upload_video_post")[0] && $("#upload_video_post")[0].files.length > 0) {
      $("#percentage-upload-video").removeClass("d-none");
      $("#percentage-upload-video").addClass("d-flex");
      upload_video = await PostCreate.uploadVideo();
    }
    let route = app.baseUrl + "/posts/save";
        let data = {
      attachments: [{ attachmentID: "1", path: "1", thumbnail: "1", type: "example" }, ...FileUpload.attachaments],
      text: $("#dropzone-uploader").val(),
      price: PostCreate.postPrice,
      requires_subscription: PostCreate.requires_subscription,
      postNotifications: PostCreate.postNotifications,
      postReleaseDate: PostCreate.postReleaseDate,
      postExpireDate: PostCreate.postExpireDate,
      long_video: upload_video,
    };

    data.type = type === "create" ? "create" : "update";
    if (type === "update") {
      data.id = postID;
    }

    $.ajax({
      type: "POST",
      data: data,
      url: route,
      success: function () {
        if (type === "create") {
          PostCreate.isSavingRedirect = true;
          PostCreate.clearDraftData(redirect(app.baseUrl + "/" + user.username));
        } else {
          redirect(app.baseUrl + "/posts/" + postID + "/" + user.username);
        }

        updateButtonState("loaded", $(".post-create-button"), trans("Save"));
        $("#confirm-post-save").modal("hide");
      },
      error: function (result) {
        if (result.status === 422 || result.status === 500) {
          $.each(result.responseJSON.errors, function (field, error) {
            if (field === "text") {
              $(".post-invalid-feedback").html(trans_choice("Your post must contain more than 10 characters.", mediaSettings.max_post_description_size, { num: mediaSettings.max_post_description_size }));
              $("#dropzone-uploader").addClass("is-invalid").focus();
            }
            if (field === "attachments") {
              $(".post-invalid-feedback").html(trans("Your post must contain at least one attachment."));
              $("#dropzone-uploader").addClass("is-invalid").focus();
            }
            if (field === "price") {
              $(".post-invalid-feedback").html(result.responseJSON.message);
              $("#dropzone-uploader").addClass("is-invalid").focus();
            }
            if (field === "permissions") {
              launchToast("danger", trans("Error"), error);
            }
          });
        } else if (result.status === 403) {
          launchToast("danger", trans("Error"), "Post not found.");
        }
        $("#confirm-post-save").modal("hide");
        $(".spinner-post-create").addClass("d-none");
        $(".post-create-button").removeClass("disabled");
        $(".post-notification-button ").removeClass("d-none");
        $(".post-price-button").removeClass("d-none");
        $(".file-upload-button").removeClass("d-none");
        $(".requires_subscription_post_container").removeClass("d-none");
        $(".requires_subscription_post_container").addClass("d-flex");
        $(".draft-clear-button").removeClass("d-none");
        $("#percentage-upload-video").addClass("d-none");
        $("#percentage-upload-video").removeClass("d-flex");
      },
    });
  },

  /**
   * Shows up the post scheduling setting setter dialog
   */
  showPostScheduleDialog: function () {
    $("#post-set-schedule-dialog").modal("show");
  },

  /**
   * Saves the post post scheduling setting into the state
   */
  savePostScheduleSettings: function () {
    if (PostCreate.postPrice !== 0 && $("#post_expire_date").val().length > 0) {
      $("#post_expire_date").addClass("is-invalid");
      return false;
    }

    PostCreate.postReleaseDate = $("#post_release_date").val().length ? $("#post_release_date").val() : null;
    PostCreate.postExpireDate = $("#post_expire_date").val().length ? $("#post_expire_date").val() : null;
    $("#post-set-schedule-dialog").modal("hide");
    $("#post_expire_date").removeClass("is-invalid");
  },
  /**
   * Clears up post scheduling setting
   */
  clearPostScheduleSettings: function () {
    PostCreate.postReleaseDate = null;
    PostCreate.postExpireDate = null;
    $("#post_release_date").val("");
    $("#post_expire_date").val("");
    $("#post_expire_date").removeClass("is-invalid");
  },
};
