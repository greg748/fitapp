import React from 'react';

export default class UnitsInput extends React.Component {
    constructor(props) {
        super(props);
        this.setUnitsInput = props.prefix + '_units';
        this.unitsOptions = [];
        this.handleUnitChange = props.handleUnitChange;

        this.state = {
            selectedValue: props.selectedValue
        }
    }

    componentWillMount(props) {
        // let options = getUnitsOptions();
        let options = ['lbs','kg','--','time'];
        this.unitsOptions = options;
    };

    handleChange(event) {
        this.setState({selectedValue : event.target.value});
        this.handleUnitChange(event);
    }

    componentDidUpdate(prevProps, prevState) {
       console.log('uis did update');
    }

    getUnitsOptions() { // api call?
        return ['lbs','kg','--','time'];
    };


    render() {
        return (
            <div className="exerciseUnits">
                <select name={this.setUnitsInput} className="setUnitsInput" value={this.state.selectedValue}
                        onChange={(e) => {this.handleChange(e)}}>
                    {
                        this.unitsOptions && this.unitsOptions.map((value) => (
                            <option key={value} value={value}>{value}</option>
                        ))
                    }
                </select>
            </div>

        );
    };


}