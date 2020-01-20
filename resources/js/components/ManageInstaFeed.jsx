import React, { useState } from 'react';
import axios from 'axios';
import MediaItem from './MediaItem';
import ManageItem from './ManageItem';
import Alert from './util/Alert';

const ManageInstaFeed = (props) => {
  const [media, setMedia] = useState({
    mediaLoaded: false,
    mediaData: {},
    generatedLink: false,
    link: null,
  });

  const [error, setError] = useState({
    displayError: false,
    alertData: {
      message: null,
      action: null,
      actionText: null,
      actionHref: null,
      fixed: true,
      class: 'primary',
    }
  });

  let alert = null;

  if (error.displayError) {
    console.log(error);
    alert = <Alert
      message={error.alertData.message}
      action={error.alertData.action}
      actionText={error.alertData.actionText}
      actionHref={error.alertData.actionHref}
      fixed={error.alertData.fixed}
      class={error.alertData.class}
    />
  }

  function refreshMedia() {
    axios.get('/getMedia')
      .then(() => {
        axios.get('/getMediaFromFile').then((obj) => {
          setMedia({
            mediaLoaded: true,
            mediaData: obj.data
          })
        });
      }).catch(() => {
        axios.get('/generateToken').then((obj) => {
          setError({
            displayError: true,
            alertData: {
              message: 'Wygeneruj odpowiedni token dostępu ',
              action: () => {
                setError({
                  displayError: false,
                  alertData: {
                    message: null,
                    action: null,
                    actionText: null,
                    actionHref: null,
                    fixed: true,
                    class: 'primary',
                  }
                });
                return true;
              },
              actionText: 'klikając w ten link',
              actionHref: obj.data,
              fixed: true,
              class: 'primary',
            }
          })
        })
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

  if (media.mediaLoaded) {

    mediaContent = media.mediaData.map((item) => {
      return <MediaItem key={item.id} mediaData={item} />
    })

  } else {
    refreshMedia();
  }

  if (media.generatedLink) {
    refreshLink = <Alert
      message='Aby odświeżyć token, '
      action={() => true}
      actionText='kliknij w ten link'
      actionHref={media.link}
      fixed={true}
      class='primary'
    />
  }

  const manageItems = [
    {
      labelText: 'Odśwież listę zdjęć z Instagrama',
      buttonText: 'Odśwież',
      buttonAction: refreshMedia
    },
    {
      labelText: 'Zmień dane klienta',
      buttonText: 'Zmień',
      buttonAction: updateClient
    },
    {
      labelText: 'Odśwież token dostępu',
      buttonText: 'Odśwież',
      buttonAction: refreshToken
    }
  ];

  const manageItemsComponents = manageItems.map((item, index) => {
    return <ManageItem key={index} data={item} />
  })
  return (
    <div className="row">
      <div className="col-md-6 mx-auto mb-4">
        <ul className="list-group">
          {manageItemsComponents}
        </ul>
        {refreshLink}
      </div>
      <div className="col-12">
        <div className="row">
          {mediaContent}
        </div>
      </div>
      {alert}
    </div>
  );
}

export default ManageInstaFeed;