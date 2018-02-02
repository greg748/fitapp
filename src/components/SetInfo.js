import React from 'react';
import UnitsInput from './UnitsInput';
import SetTypesInput from './SetTypesInput';
import { debounce } from 'underscore';

class SetInfo extends React.Component {
    constructor(props) {
        super(props);
        this.prefix = 'ex_' + props.groupOrdinal + '_' + props.exercise + '_' +props.set;
        this.weightInput = this.prefix + '_weight';
        this.repsInput = this.prefix + '_reps';
        this.setInfo = props.setInfo;
        this.processSetChange = debounce(props.processSetChange, 1000).bind(this);
        this.handleUnitChange = this.handleUnitChange.bind(this);
        this.handleTypeChange = this.handleTypeChange.bind(this);
        this.state = {
            group: props.groupOrdinal,
            exercise_id : props.exercise,
            setOrdinal: props.set,
            weight: (!!this.setInfo.weight) ? this.setInfo.weight : 0,
            reps: (!!this.setInfo.reps) ? this.setInfo.reps : 0,
            units: (!!this.setInfo.units) ? this.setInfo.units : 'lb',
            type: (!!this.setInfo.type) ? this.setInfo.type : 'bilateral'

        };
    }

    handleRepChange(event) {
        console.log('hrc', event.target.value, event.target.name);
        this.setState({reps : event.target.value});
    }

    handleWeightChange(event) {
        console.log('hwc', event.target.value, event.target.name);
        this.setState({weight : event.target.value});
    }

    handleUnitChange(event) {
        console.log('huc', event.target.value, event.target.name);
        this.setState({units : event.target.value});
    };

    handleTypeChange(event) {
        console.log('htc', event.target.value, event.target.name);
        this.setState({type: event.target.value});
    };


    componentDidUpdate(prevProps, prevState) {
        if (prevState !== this.state) {
            this.processSetChange(this.state);
        }
    }

    render = () => (
        <div className="setBlock" key={this.prefix}>
            <form id={this.prefix}>
                <input type="hidden" name="unitHolder" value={this.state.units}/>
                <input type="hidden" name="typeHolder" value={this.state.type}/>
            <SetTypesInput
                prefix={this.prefix}
                selectedValue={this.state.type}
                handleTypeChange={this.handleTypeChange}
                processSetChange={this.processSetChange}
            />
            <div className="exerciseWeight">
                <label>Weight:</label> <input className="weightInput" type="number" step="0.1" name={this.weightInput} onChange={(e) => {this.handleWeightChange(e);}}/>
            <UnitsInput prefix={this.prefix} selectedValue={this.state.units}
                        handleUnitChange={this.handleUnitChange}
                        processSetChange={this.processSetChange}
            />
            </div>
                <div className="exerciseReps"><label>Reps:</label>
                    <input type="number" step="1"
                         className="repsInput"
                         name={this.repsInput}
                         value={this.state.reps}
                         onChange={(e) => {this.handleRepChange(e);}}/>
                </div>
            </form>
        </div>
    );

}

export default SetInfo;

