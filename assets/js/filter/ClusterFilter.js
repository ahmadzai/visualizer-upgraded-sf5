import $ from 'jquery'
import Filter from "./Filter";

class ClusterFilter {
    constructor() {
        $('#filterCampaign').multiselect({
            nonSelectedText: 'Campaigns',
            numberDisplayed: 2,
            maxHeight: 250,
            onChange: function(element, checked) {
                //console.log(element[0].value);
                Filter.resetFilter('filterCluster');
                Filter.resetFilter('filterDistrict');
                Filter.resetFilter('filterProvince');

                let campaign = $('#filterCampaign option:selected');
                let selectedCampaigns = [];
                $(campaign).each(function (index, region) {
                    selectedCampaigns.push([$(this).val()]);
                });

                if(selectedCampaigns.length > 0) {
                    let data = {
                        'source': $('#ajaxUrl').data('source'),
                        'campaign':selectedCampaigns,
                    };
                    Filter.ajaxRequest('filter_province_clst', data, 'filterProvince');
                }
            }
        });

        $('#filterProvince').multiselect({
            nonSelectedText: 'Province',
            numberDisplayed: 2,
            maxHeight: 300,
            enableClickableOptGroups: true,
            onDeselectAll: function () {
                //alert('Deselect is called');
                Filter.resetFilter('filterDistrict');
                Filter.resetFilter('filterCluster');
            },
            onChange: function(element, checked) {
                Filter.resetFilter('filterCluster');
                var province = $('#filterProvince option:selected');
                var selectedProvinces = [];
                $(province).each(function (index, province) {
                    selectedProvinces.push([$(this).val()]);
                });
                if(selectedProvinces.length > 0) {
                    var data = {
                        "province": selectedProvinces,
                        "risk" : false,
                        'campaign':$('#filterCampaign').val(),
                        'source': $('#ajaxUrl').data('source'),
                    };
                    Filter.ajaxRequest('filter_district', data, 'filterDistrict');

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
                Filter.resetFilter('filterCluster');
                var selectedDistricts = [];
                var district = $('#filterDistrict option:selected');
                //console.log(district);
                //if(district.indexOf('VHR')>-1 || district.indexOf('HR')>-1 || district.indexOf(null)>-1) {
                $(district).each(function (index, district) {
                    selectedDistricts.push([$(this).val()]);
                });

                var districts = selectedDistricts.join(',');
                districts = districts.split(',');
                if(districts.length > 2) {
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

                    var campaign = $('#filterCampaign').val();
                    //console.log(campaign);
                    var data = {
                        "district": selectedDistricts,
                        'campaign': campaign,
                        'source':$('.data-source').data('source'),

                    };
                    Filter.ajaxRequest('filter_cluster', data, 'filterCluster');

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
            allSelectedText: 'All clusters'
        });
        // bind the data to to the filter multiselect
        let clusters = $('#pre-loaded-clusters').val();
        if(clusters != '-1')
            $('#filterCluster').multiselect('dataprovider', JSON.parse(clusters))
    }
}

export default ClusterFilter;