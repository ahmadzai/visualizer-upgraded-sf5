/*
This Class is Just Used to Capture Value of the Filters (Dropdown)
And this can be used for all dashboard having Filter up to District Level
 */
import $ from 'jquery'
import Alerts from '../common/Alerts';

class FilterListener {
    listenMain() {

        let campaigns = $('#filterCampaign').val();

        let region = $('#filterRegion').val();

        let provinces = $('#filterProvince').val();

        let districts = $('#filterDistrict').val();

        let entity = $('#ajaxUrl').data('source');

        // // check if a user didn't select anything, then return
        // if(campaigns === null || campaigns === undefined) {
        //     Alerts.error("Please select at least one campaign");
        //     return;
        // }

        //Todo: A bit more logic to control which api should be called
        return {
            'campaign':campaigns,
            region,
            'province': provinces,
            'district': districts,
            entity
        };

    }

    listenCluster() {
        let selectedCampaigns = $('#filterCampaign').val();
        //var provinces = $('#filterProvince option:selected');
        let selectedProvinces = $('#filterProvince').val();
        //var districts = $('#filterDistrict option:selected');
        let selectedDistricts = $('#filterDistrict').val();
        //var clusters = $('#filterCluster option:selected');
        let selectedClusters = $('#filterCluster').val();

        let entity = $('#ajaxUrl').data('source');


        return {
            'campaign':selectedCampaigns,
            'province': selectedProvinces,
            'district': selectedDistricts,
            'cluster':selectedClusters,
            entity
        };
    }

    listenCcsSm() {

        let campaigns = $('#filterCampaign').val();

        let provinces = $('#filterProvince').val();

        let districts = $('#filterDistrict').val();

        let clusters = $('#filterCluster').val();

        return  {
            'campaign':campaigns,
            'cluster':clusters,
            'province': provinces,
            'district': districts
        };
    }

    listenBphs() {

        let campaigns = $('#filterCampaign').val();

        let provinces = $('#filterProvince').val();

        let districts = $('#filterDistrict').val();

        let facilities = $('#filterFacility').val();

        return  {
            'campaign':campaigns,
            'facility':facilities,
            'province': provinces,
            'district': districts
        };
    }
}

export default FilterListener;