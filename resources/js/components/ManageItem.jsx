import React, { setState } from 'react';

const ManageItem = (props) => {
    const data = props.data;
    const [loading, setLoading] = setState()
    return (
        <li className="list-group-item">
            <div className="d-flex justify-content-between align-items-center">
                <span>{data.labelText}</span>
                <button onClick={() => { data.buttonAction() }} className="btn btn-primary">
                    <span className={"spinner-border spinner-border-sm"} role="status" aria-hidden="true"></span>
                    <span className={"sr-only"}>Loading...</span>
                    {data.buttonText}
                </button>
            </div>
        </li>
    );
}

export default ManageItem;