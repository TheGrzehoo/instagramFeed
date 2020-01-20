import React from 'react';

const Loading = (props) => {
  let loadingFixed = {
    position: 'fixed',
    top: 0,
    left: 0,
    right: 0,
    bottom: 0,
    background: 'rgba(255,255,255,0.6)',
    zIndex: 999,
  }
  return (
    <div className="d-flex align-items-center justify-content-center" style={props.fixed ? loadingFixed : {}}>
      <div className="spinner-grow text-primary" role="status">
        <span className="sr-only">Loading...</span>
      </div>
    </div>
  );
}

export default Loading;