$(function () {
  var myPlaylist = new jPlayerPlaylist(
    {
      jPlayer: "#jplayer_N",
      cssSelectorAncestor: "#jp_container_N",
    },
    [
      {
        title: "Party Anthemes",
        artist: "Shreya ghosal",
        mp3: "https://eu-central.storage.cloudconvert.com/tasks/d2f86c9f-d9d8-448a-bc4d-9b212ee8e51b/sunrahahai.mp3?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Content-Sha256=UNSIGNED-PAYLOAD&X-Amz-Credential=cloudconvert-production%2F20240228%2Ffra%2Fs3%2Faws4_request&X-Amz-Date=20240228T080215Z&X-Amz-Expires=86400&X-Amz-Signature=47fb93e53f722d4c368126145ec63145b77b964bbb42232107694ae88a76a7a1&X-Amz-SignedHeaders=host&response-content-disposition=inline%3B%20filename%3D%22sunrahahai.mp3%22&response-content-type=audio%2Fmpeg&x-id=GetObject",
        poster: "../assets/images/music/album3.jpg",
      },
      {
        title: "Shubhaarambh",
        artist: "Shreya ghosal",
        mp3: "https://eu-central.storage.cloudconvert.com/tasks/79b8d3d1-3c18-4ae6-ab6f-bbbfc5b392b1/shubhaarambh.mp3?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Content-Sha256=UNSIGNED-PAYLOAD&X-Amz-Credential=cloudconvert-production%2F20240228%2Ffra%2Fs3%2Faws4_request&X-Amz-Date=20240228T074750Z&X-Amz-Expires=86400&X-Amz-Signature=9f9b92de9f1484c54c0159817ec3e5a139b500d5a2fff94dd89cd4df59267bc6&X-Amz-SignedHeaders=host&response-content-disposition=inline%3B%20filename%3D%22shubhaarambh.mp3%22&response-content-type=audio%2Fmpeg&x-id=GetObject",
        poster: "../assets/images/music/album2.jpg",
      },
      {
        title: "title2",
        artist: "Shreya ghosal",
        mp3: "https://eu-central.storage.cloudconvert.com/tasks/d2f86c9f-d9d8-448a-bc4d-9b212ee8e51b/sunrahahai.mp3?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Content-Sha256=UNSIGNED-PAYLOAD&X-Amz-Credential=cloudconvert-production%2F20240228%2Ffra%2Fs3%2Faws4_request&X-Amz-Date=20240228T080215Z&X-Amz-Expires=86400&X-Amz-Signature=47fb93e53f722d4c368126145ec63145b77b964bbb42232107694ae88a76a7a1&X-Amz-SignedHeaders=host&response-content-disposition=inline%3B%20filename%3D%22sunrahahai.mp3%22&response-content-type=audio%2Fmpeg&x-id=GetObject",
        poster: "../assets/images/music/album1.jpg",
      },
    ],
    {
      playlistOptions: {
        enableRemoveControls: true,
        autoPlay: false,
      },
      swfPath: "./../dist/js/jplayer/",
      supplied: "webmv, ogv, m4v, oga, mp3",
      smoothPlayBar: true,
      keyEnabled: true,
      audioFullScreen: false,
    }
  );

  $(document).on(
    $.jPlayer.event.pause,
    myPlaylist.cssSelector.jPlayer,
    function () {
      $(".musicbar").removeClass("animate");
      $(".jp-play-me").removeClass("active");
      $(".jp-play-me").parent("li").removeClass("active");
    }
  );

  $(document).on(
    $.jPlayer.event.play,
    myPlaylist.cssSelector.jPlayer,
    function () {
      $(".musicbar").addClass("animate");
    }
  );

  $(document).on("click", ".jp-play-me", function (e) {
    e && e.preventDefault();
    var $this = $(e.target);
    if (!$this.is("a")) $this = $this.closest("a");

    $(".jp-play-me").not($this).removeClass("active");
    $(".jp-play-me").parent("li").not($this.parent("li")).removeClass("active");

    $this.toggleClass("active");
    $this.parent("li").toggleClass("active");
    if (!$this.hasClass("active")) {
      myPlaylist.pause();
    } else {
      var i = Math.floor(Math.random() * (1 + 7 - 1));
      myPlaylist.play(i);
    }
  });

  $("#playlist").on("shown.bs.dropdown", function () {
    $(".mplayer .jp-playlist.open ul").css({
      left: "auto",
      right: "-21px",
      width: "300px",
    });
  });

  // for video player

  $("#jplayer_1").jPlayer({
    ready: function () {
      $(this).jPlayer("setMedia", {
        title: "The Asgardian Hammer",
        m4v: "dist/avi/vd.mp4",
        poster: "dist/avi/vd.jpg",
      });
    },
    swfPath: "dist/js/jPlayer",
    supplied: "webmv, ogv, m4v",
    size: {
      width: "100%",
      height: "auto",
      cssClass: "jp-video-360p",
    },
    globalVolume: true,
    smoothPlayBar: true,
    keyEnabled: true,
  });
});
