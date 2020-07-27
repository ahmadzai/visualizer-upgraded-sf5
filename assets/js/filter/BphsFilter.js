import $ from 'jquery'
import Filter from "./Filter";

class BphsFilter {
    constructor() {
        $('#filterCampaign').multiselect({
            nonSelectedText: 'Months',
            numberDisplayed: 2,
            maxHeight: 250,
            onChange:function(element, checked) {
                Filter.resetFilter('filterProvince');
                Filter.resetFilter('filterDistrict');
                Filter.resetFilter('filterFacility');
                let campaigns = $('#filterCampaign').val();
                //console.log(campaigns);
                campaigns.length > 1 ?
                    $('.btn-cum-res').show() :
                    $('.btn-cum-res').hide();

                if(campaigns.length > 0) {

                    let data = {
                        'campaign':campaigns
                    };
                    Filter.ajaxRequest('bphs_filter_province', data, 'filterProvince');

                }

            }
        });

        $('#filterProvince').multiselect({
            nonSelectedText: 'Province',
            numberDisplayed: 2,
            maxHeight: 300,
            enableClickableOptGroups: true,
            enableCaseInsensitiveFiltering: true,
            onChange: function(element, checked) {
                Filter.resetFilter('filterDistrict');
                Filter.resetFilter('filterFacility');
                let province = $('#filterProvince option:selected');
                let selectedProvinces = [];
                $(province).each(function (index, province) {
                    selectedProvinces.push([$(this).val()]);
                });
                if(selectedProvinces.length > 0) {

                    let data = {
                        "province": selectedProvinces,
                        'campaign':$('#filterCampaign').val()
                    };
                    Filter.ajaxRequest('bphs_filter_district', data, 'filterDistrict');

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
                Filter.resetFilter('filterFacility');
                
                $(district).each(function (index, district) {
                    selectedDistricts.push([$(this).val()]);
                });
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
                    Filter.ajaxRequest('bphs_filter_facility', data, 'filterFacility');

                }

            }
        });

        $('#filterFacility').multiselect({
            nonSelectedText: 'Facility',
            numberDisplayed: 2,
            maxHeight: 500,
            enableCaseInsensitiveFiltering: true,
            enableClickableOptGroups: true,
            includeSelectAllOption: true,
            allSelectedText: 'All'
        });

    }
}

export default BphsFilter;