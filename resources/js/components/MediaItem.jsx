import React from 'react';

const MediaItem = (props) => {
  return (
    <div className="col-md-4 mb-4" key={props.mediaData.id}>
      <a className="d-block" href={props.mediaData.permalink}>
        <img className="d-block" style={{maxWidth: '100%'}} src={props.mediaData.media_url} alt={props.mediaData.id} />
      </a>
    </div>
  );
}

export default MediaItem;