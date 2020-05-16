import $ from 'jquery'
import Filter from "./Filter";
import Routing from "../common/Routing";

class MainFilter {
    constructor() {

        let defaultDistrict =
            [
                {label: 'All districts', value: 'All'},
                {label: 'VHR districts', value: 'VHR'},
                {label: 'Focus districts', value: 'Focus'},
                {label: 'HR districts', value: 'HR'},
                {label: 'Non-V/HR districts', value: 'None'}
            ];

        $('#filterCampaign').multiselect({
            nonSelectedText: 'Campaigns',
            numberDisplayed: 2,
            maxHeight: 250,
            onChange: function(element, checked) {
                //console.log(element[0].value);
                Filter.resetFilter('filterDistrict', defaultDistrict);
                Filter.resetFilter('filterProvince');
                Filter.resetFilter('filterRegion');

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
                    Filter.ajaxRequest('filter_region', data, 'filterRegion');
                }
            }
        });

        $('#filterRegion').multiselect({
            nonSelectedText: 'Region',
            includeSelectAllOption: true,
            onSelectAll: function() {
                Filter.resetFilter('filterDistrict', defaultDistrict);
                let region = $('#filterRegion option:selected');
                let selectedRegions = [];
                $(region).each(function (index, region) {
                    selectedRegions.push([$(this).val()]);
                });

                if(selectedRegions.length > 0) {
                    let data = {
                        "region": selectedRegions,
                        'source': $('#ajaxUrl').data('source'),
                        'campaign':$('#filterCampaign').val(),
                    };
                    Filter.ajaxRequest('filter_province', data, 'filterProvince');
                }
            },
            onDeselectAll: function () {
                Filter.resetFilter('filterDistrict');
                Filter.resetFilter('filterProvince');
            },
            onChange: function(element, checked) {
                //console.log(element[0].value);
                Filter.resetFilter('filterDistrict', defaultDistrict);
                let region = $('#filterRegion option:selected');
                let selectedRegions = [];
                $(region).each(function (index, region) {
                    selectedRegions.push([$(this).val()]);
                });

                if(selectedRegions.length > 0) {
                    let data = {
                        "region": selectedRegions,
                        'source': $('#ajaxUrl').data('source'),
                        'campaign':$('#filterCampaign').val(),
                    };
                    Filter.ajaxRequest('filter_province', data, 'filterProvince');
                }
            }
        });

        $('#filterProvince').multiselect({
            nonSelectedText: 'Province',
            numberDisplayed: 2,
            maxHeight: 300,
            enableClickableOptGroups: true,
            includeSelectAllOption: true,
            onChange: function(element, checked) {
                Filter.resetFilter('filterDistrict', defaultDistrict);
                let province = $('#filterProvince option:selected');
                let selectedProvinces = [];
                $(province).each(function (index, province) {
                    selectedProvinces.push([$(this).val()]);
                });
                if(selectedProvinces.length > 0) {
                    let data = {
                        "province": selectedProvinces,
                        'campaign':$('#filterCampaign').val(),
                        'source': $('#ajaxUrl').data('source')
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

                // if(element.val() === "All") {
                //     if(checked) {
                //         // select all
                //         $("#filterDistrict").multiselect('selectAll');
                //         $('#filterDistrict').multiselect('deselect', ['HR', 'VHR', 'None', 'Focus']);
                //     }
                // }

                let selectedDistricts = [];
                let district = $('#filterDistrict option:selected');
                $(district).each(function (index, district) {
                    selectedDistricts.push([$(this).val()]);
                });

                let districts = selectedDistricts.join(',');
                districts = districts.split(',');

                if(districts.length > 1) {
                    if(districts.includes('VHR') || districts.includes('HR') ||
                        districts.includes('None') || districts.includes('Focus') ||
                        districts.includes('All')) {
                        $.each(districts, function (index, value) {
                            if (value !== 'VHR' || value !== 'None' ||
                                value !== 'HR' || value !== 'Focus' || value !== 'All') {
                                $('#filterDistrict').multiselect('deselect', value, true);
                            }
                        })
                    }
                }

                let route = $('.btn-cluster').data('route');
                if(selectedDistricts.length === 1) {
                    let districtId = $('#filterDistrict').val();
                    if(districtId[0].indexOf("HR") == -1 && districtId.includes('All') === false) {
                        let url = Routing.generate(route, {'district': districtId})
                        $('.btn-cluster').html("<i class='fa fa-mail-forward'></i> Clusters' data for " +
                            $('#filterDistrict option:selected').attr('label'));
                        $('.btn-cluster').attr("href", url);
                    }
                } else {
                    $('.btn-cluster').html("<i class='fa fa-mail-forward'></i> For clusters' data click here!");
                    $('.btn-cluster').attr("href", Routing.generate(route));
                }

            }
        });
    }
}

export default MainFilter;