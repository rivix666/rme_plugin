Amplitude.init({
  "songs": [
    {
      //"name": "Offcut #6",
      //"artist": "Little People",
      //"album": "We Are But Hunks of Wood Remixes",
      "url": "https://radiomaxelektro.pl/wp-content/uploads/music-clip.mp3",
      //"cover_art_url": "../album-art/we-are-but-hunks-of-wood.jpg"
    }
  ]
});

document.getElementById('song-played-progress-1').addEventListener('click', function( e ){
  if( Amplitude.getActiveIndex() == 0 ){
    var offset = this.getBoundingClientRect();
    var x = e.pageX - offset.left;

    Amplitude.setSongPlayedPercentage( ( parseFloat( x ) / parseFloat( this.offsetWidth) ) * 100 );
  }
});
