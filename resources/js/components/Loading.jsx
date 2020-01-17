import React from 'react';

const Loading = () => {
  return (
    <div style={{height: '100vh'}} className="d-flex align-items-center">
      <div className="spinner-grow text-primary" role="status">
        <span className="sr-only">Loading...</span>
      </div>
    </div>
  );
}

export default Loading;