'use strict';

import _ from 'underscore';

class FilterControl {

    setFilterState(state) {
        this.state = {...state};
    }

    checkFilterState(newState) {
        if(_.isEqual(this.state, newState)) {
            //Alerts.filterInfo();
            return false;
        }

        // cases what changed
        let multiCampaign = newState.campaign.length > 1;

        let regionChanged = !_.isEqual(newState.region, this.state.region);
        let provinceChanged = !_.isEqual(newState.province, this.state.province);
        let districtChanged = !_.isEqual(newState.district, this.state.district);

        let anyChange = regionChanged || provinceChanged || districtChanged;

        // set new state
        this.setFilterState(newState);

        if(anyChange) return 'both';
        else if(multiCampaign) return 'trend';
        else return 'info';

    }

    checkClusterFilterState(newState) {
        if(_.isEqual(this.state, newState)) {
            //Alerts.filterInfo();
            return false;
        }

        // cases what changed
        let multiCampaign = newState.campaign.length > 1;

        let provinceChanged = newState.province !== this.state.province;
        let districtChanged = newState.district !== this.state.district;
        let clusterChanged = !_.isEqual(newState.cluster, this.state.cluster);

        let anyChange = clusterChanged || provinceChanged || districtChanged;

        // set new state
        this.setFilterState(newState);

        if(anyChange) return 'both';
        else if(multiCampaign) return 'trend';
        else return 'info';

    }

    ccsSmFilterState(newState) {
        if(_.isEqual(this.state, newState)) {
            return false;
        } else {
            this.setFilterState(newState);
            return true;
        }

    }
}

export default FilterControl;