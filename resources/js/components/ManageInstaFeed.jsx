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

  function refreshMedia() {
    axios.get('/api/getMedia')
      .then((obj) => {
        if(obj.data.error) {
          setError({
            displayError: true,
            alertData: {
              message: 'Odśwież token dostępu',
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
              actionText: 'Zamknij',
              actionHref: '##',
              fixed: true,
              class: 'warning',
            }
          })
        } else {
          axios.get('/api/getMediaFromFile').then((obj) => {
            setMedia({
              mediaLoaded: true,
              mediaData: obj.data
            })
          });
        }
      }).catch(() => {
        axios.get('/api/generateToken').then((obj) => {
          console.log(obj);
          if (obj.data.error) {
            setError({
              displayError: true,
              alertData: {
                message: 'Niepoprawne dane klienta. ',
                action: () => {
                  updateClient();
                  return true;
                },
                actionText: 'Wprowadź je jeszcze raz',
                actionHref: '##',
                fixed: true,
                class: 'danger',
              }
            })
          } else {
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
                actionHref: obj.data.data,
                fixed: true,
                class: 'primary',
              }
            })
          }
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
    axios.get('/api/generateToken').then((obj) => {
      if(obj.data.error) {
        setError({
          displayError: true,
          alertData: {
            message: 'Nieprawidłowe dane klienta. ',
            action: () => {
              updateClient();
            },
            actionText: 'Zmień dane.',
            actionHref: '##',
            fixed: true,
            class: 'warning',
          }
        })
      } else {
        setMedia({
          generatedLink: true,
          link: obj.data.data
        })
      }
    })
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

  const alert = <Alert
    message={error.alertData.message}
    action={error.alertData.action}
    actionText={error.alertData.actionText}
    actionHref={error.alertData.actionHref}
    fixed={error.alertData.fixed}
    class={error.alertData.class}
  />

  const loadMediaContent = () => media.mediaData.map((item) => {
    return <MediaItem key={item.id} mediaData={item} />
  })

  const refreshLink = <Alert
    message='Aby odświeżyć token, '
    action={() => true}
    actionText='kliknij w ten link'
    actionHref={media.link}
    fixed={true}
    class='primary'
  />

  return (
    <div className="row">
      <div className="col-md-6 mx-auto mb-4">
        <ul className="list-group">
          {manageItemsComponents}
        </ul>
        {media.generatedLink && refreshLink}
      </div>
      <div className="col-12">
        <div className="row">
          {media.mediaLoaded && loadMediaContent()}
        </div>
      </div>
      {error.displayError && alert}
    </div>
  );
}

export default ManageInstaFeed;