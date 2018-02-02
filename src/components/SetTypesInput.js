import React from 'react';

export default class SetTypesInput extends React.Component {
    constructor(props) {
        super(props);
        this.setTypesInput = props.prefix + '_type';
        this.setTypesOptions = [];
        this.handleTypeChange = props.handleTypeChange;
        this.state = {
            selectedValue: props.selectedValue
        }
    }

    componentWillMount(props) {
        // let options = getUnitsOptions();
        let options = ['bilateral','single','alt','position-1','position-2','position-3','time','alt-high','alt-low'];
        this.setTypesOptions = options;
    };

    componentDidUpdate() {
        console.log("sti did update",this.state);
    }


    handleChange(event) {
        this.setState({selectedValue : event.target.value});
        this.handleTypeChange(event);
    }

    getSetTypesOptions() {
        return ['bilateral','single','alt','position-1','position-2','position-3','time','alt-high','alt-low'];
    };


    render() {
        return (
            <div className="exerciseSetType">
                <select
                    name={this.setTypesInput}
                    className="setTypesInput"
                    value={this.state.selectedValue}
                    onChange={(e) => {this.handleChange(e);}}
                >
                    {
                        this.setTypesOptions && this.setTypesOptions.map((value) => (
                            <option key={value} value={value}>{value}</option>
                        ))
                    }
                </select>
            </div>

        );
    };


}