import React, { setState } from 'react';

const ManageItem = (props) => {
    const data = props.data;
    return (
        <li className="list-group-item">
            <div className="d-flex justify-content-between align-items-center">
                <span>{data.labelText}</span>
                <button onClick={() => { data.buttonAction() }} className="btn btn-primary">
                    {data.buttonText}
                </button>
            </div>
        </li>
    );
}

export default ManageItem;