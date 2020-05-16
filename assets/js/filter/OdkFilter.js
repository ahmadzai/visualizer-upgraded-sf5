import $ from 'jquery'
import Filter from "./Filter";

class OdkFilter {
    constructor() {
        $('#filterCampaign').multiselect({
            nonSelectedText: 'Months',
            numberDisplayed: 2,
            maxHeight: 250,
            onChange:function(element, checked) {
                let campaigns = $('#filterCampaign').val();
                //console.log(campaigns);
                campaigns.length > 1 ?
                    $('.btn-cum-res').show() :
                    $('.btn-cum-res').hide();

            }
        });

        $('#filterProvince').multiselect({
            nonSelectedText: 'Province',
            numberDisplayed: 2,
            maxHeight: 300,
            enableClickableOptGroups: true,
            onChange: function(element, checked) {
                Filter.resetFilter('filterDistrict');
                Filter.resetFilter('filterCluster');
                let province = $('#filterProvince option:selected');
                let selectedProvinces = [];
                $(province).each(function (index, province) {
                    selectedProvinces.push([$(this).val()]);
                });
                if(selectedProvinces.length > 0) {
                    let source = $('#icn_table').data('source');
                    let data = {
                        "source": source,
                        "province": selectedProvinces,
                        "risk" : false,
                        'campaign':$('#filterCampaign').val()
                    };
                    Filter.ajaxRequest('filter_district_odk', data, 'filterDistrict');

                }
            }
        });

        $('#filterDistrict').multiselect({
            nonSelectedText: 'District',
            numberDisplayed: 2,
            maxHeight: 300,
            enableCaseInsensitiveFiltering: true,
            enableClickableOptGroups: true,
            onChange: function (element, checked) {
                let selectedDistricts = [];
                let district = $('#filterDistrict option:selected');
                Filter.resetFilter('filterCluster');
                
                $(district).each(function (index, district) {
                    selectedDistricts.push([$(this).val()]);
                });

                let districts = selectedDistricts.join(',');
                districts = districts.split(',');
                if(districts.length > 1) {
                    if(districts.indexOf('VHR')>-1 || districts.indexOf('HR')>-1 || 
                        districts.indexOf('Non-V/HR districts')>-1) {
                        $.each(districts, function (index, value) {
                            if (value !== 'VHR' || value !== 'Non-V/HR districts' || value !== 'HR') {
                                $('#filterDistrict').multiselect('deselect', value, true);
                            }
                        })
                    }
                }

                if(selectedDistricts.length > 0) {
                    selectedDistricts = $('#filterDistrict').val();
                    let campaign = $('#filterCampaign').val();
                    //console.log(campaign);
                    let source = $('#icn_table').data('source');
                    let data = {
                        "district": selectedDistricts,
                        'campaign': campaign,
                        'source':source
                    };
                    Filter.ajaxRequest('filter_cluster_odk', data, 'filterCluster');

                }

            }
        });

        $('#filterCluster').multiselect({
            nonSelectedText: 'Clusters',
            numberDisplayed: 2,
            maxHeight: 500,
            enableCaseInsensitiveFiltering: true,
            enableClickableOptGroups: true,
            includeSelectAllOption: true,
            allSelectedText: 'All'
        });

    }
}

export default OdkFilter;