import React, { useState } from 'react';
import axios from 'axios';
import MediaItem from './MediaItem';

const ManageInstaFeed = (props) => {
  const [media, setMedia] = useState({
    mediaLoaded: false,
    mediaData: {},
    generatedLink: false,
    link: ''
  })

  function refreshMedia() {
     axios.get('/getMedia')
       .then(() => {
        axios.get('/getMediaFromFile').then((obj) => {
          setMedia({
            mediaLoaded: true,
            mediaData: obj.data
          })
         });
      });
  }

  function updateClient() {
    props.refreshClient({
      clientData: false,
      refreshPage: false
    })
  }

  function refreshToken() {
    axios.get('/generateToken').then((obj) => {
      setMedia({
        generatedLink: true,
        link: obj.data
      })
    })
  }

  let mediaContent, refreshLink;

  if (media.mediaLoaded){
    
    mediaContent = media.mediaData.map((item) => {
      return <MediaItem mediaData={item} />
    })
    
  }

  if (media.generatedLink) {
    console.log('elo');
    refreshLink = <a href={media.link}>Kliknij, aby odświeżyć token</a>
  }
  return (
    <div className="my-5 d-flex flex-wrap justify-content-center">
      <div className="col-12 mb-4">
        <label htmlFor="refreshInsta" className="mr-4">Odśwież listę zdjęć z Instagrama</label>
        <button onClick={() => refreshMedia()} className="btn btn-primary" id="refreshInsta">Odśwież</button>
      </div>
      <div className="col-12 mb-4">
        <label htmlFor="refreshClient" className="mr-4">Zmień dane klienta</label>
        <button onClick={() => {updateClient()}} className="btn btn-primary" id="refreshClient">Zmień</button>
      </div>
      <div className="col-12 mb-4">
        <label htmlFor="refreshToken" className="mr-4">Odśwież token dostępu</label>
        <button onClick={() => {refreshToken()}} className="btn btn-primary" id="refreshToken">Odśwież</button>
        {refreshLink}
      </div>
      <div className="row">
        {mediaContent}
      </div>
    </div>
  );
}

export default ManageInstaFeed;