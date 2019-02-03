@extends($layout) 
@section('content')
<div class="container">
    <h3>New Booking</h3>
    <div>
        <br> @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <form method="post" action="/bookings">
            @csrf
            <table>
                <tr>
                    <td><label>Where? </label></td>
                    <td>
                        <select id="selectarea" name="area">
                            <option value="-1">Select a city</option>
                            @foreach ($areas as $area)
                                <option value="{{$area->id}}">{{$area->name}}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label>How many travellers? </label></td>
                    <td><input type="number" id="travellerscount" name="headcount" min="1" max="15" value="1" /></td>
                </tr>
                <tr>
                    <td><label>How many rooms? </label></td>
                    <td><input type="number" id="roomscount" name="roomcount" min="1" max="10" value="1" /></td>
                </tr>
                <tr>
                    <td><label>When? </label></td>
                    <td><input type="text" name="dates" size="25" /><span id="numberOfNights"></span></td>
                </tr>
                <tr>
                    <td><label>Enjoy hotel up to: </label></td>
                    <td>
                        <input id="starslider" name="stars" type="range" size="20" min="2" max="5" value="1">&nbsp;
                        <span id="star"></span>
                    </td>
                </tr>
            </table>
            <br>
            <div id="price"></div>
            <a id="findhotels" href="#" class="btn btn-primary">Find hotels</a>
            <button type="submit" id="booknow" class="btn btn-primary">Book Now</button>
        </form>
        <br>
        <img id="fetch-hotel-spinner" src="/svg/spinner.svg" class="d-none" />
        <div id="hotelpanel" class="card d-none">
            <h5 class="card-header">Hotels in range</h5>
            <div id="pagenumbers" style="padding: 0.75rem 1.25rem;">
            </div>
            <hr>
            <div id="fetch-hotel-result">
            </div>
        </div>
    </div>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
        crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jsrender/1.0.1/jsrender.min.js"></script>
    <script id="hotelTmpl" type="text/x-jsrender">
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4">
                    <img src="https://www.kayak.com@{{:thumburl}}" />
                </div>
                <div class="col-12 col-md-6 col-lg-8">
                    <h5 class="card-title">@{{:name}}</h5>
                    <p class="card-text">Stars:@{{:stars}}</p>
                    <a href="https://www.google.com/maps/search/?api=1&query=@{{:lat}},@{{:lon}}">View on map</a>
                </div>
            </div>
        </div>
        <hr/>
    </script>
    <script>
        var tmpl = $.templates("#hotelTmpl");
        
        var hotels = [];
        var currentPage = 1;
        var pageSize = 20;
        var checkInDate = getTodaysDate();
        var checkOutDate = getTodaysDate();
        $('input[name="dates"]').daterangepicker({
            locale: {
                format: 'YYYY-MM-DD'
            },
            minDate: 0
        }, function(start, end, label){
            checkInDate = start;
            checkOutDate = end;
            $('#numberOfNights').html(" " + getNumberOfNights() + " nights");
        });
        
        $(document).ready(function() {
            var values = [2, 3, 4, 5];
            $('#starslider').change(function() {
                $('#star').text(this.value + ' stars');
            });

            $('#star').text($('#starslider')[0].value + ' stars');
        });

        $('#findhotels').on('click', function(e) {
            e.preventDefault();
            fetchHotels();
        });

        function fetchHotels() {
            let selectedCtid = $('#selectarea')[0].selectedOptions[0].value;
            if (selectedCtid != -1) {
                $('#fetch-hotel-spinner').removeClass("d-none");
                $('#hotelpanel').addClass("d-none");
                $.ajax({
                    url: "https://apidojo-kayak-v1.p.rapidapi.com/hotels/create-session?currency=MYR&rooms=" + $('#roomscount').val() + "&citycode=" + $('#selectarea')[0].selectedOptions[0].value + "&checkin=" + checkInDate.toISOString().substring(0, 10) + "&checkout=" + checkOutDate.toISOString().substring(0, 10) + "&adults=" + $('#travellerscount').val(),
                    type: "GET",
                    beforeSend: function(xhr){xhr.setRequestHeader('X-RapidAPI-Key', '08739c7a37mshf5cc3c651d13831p1cd6e1jsn1cdd45a46b0d');},
                    success: function(response) { 
                        $('#fetch-hotel-spinner').addClass("d-none");
                        //alert('Success! Hotels found: ' + response.hotelset.length); 
                        hotels = response.hotelset;
                        if (hotels) {
                            loadHotels();
                            $('#hotelpanel').removeClass("d-none");
                        }
                        else {
                            $('#fetch-hotel-spinner').addClass("d-none");
                            alert('No record found!');
                        }
                    },
                    error: function(error) {
                        $('#fetch-hotel-spinner').addClass("d-none");
                        alert('Fetch error!');
                        console.log(error);
                    }
                });
            }
            else {
                alert("Please select a city from the list");
            }
        }

        function loadHotels() {
            let maximumStars = $('#starslider')[0].value;
            hotels = $.grep(hotels, function(item, index){
                return (item.stars <= maximumStars); 
            });
            loadPage();
            loadPageNumbers();
            calculatePrice();
        }

        function loadPageNumbers() {
            let totalNumberOfPages = Math.ceil(hotels.length / pageSize);
            var i;
            var pageNumbersHtml = "";
            for (i = 1; i <= totalNumberOfPages; i++) { 
                pageNumbersHtml += "<a href='#' class='pagenumber btn btn-sm btn-primary " + (i == currentPage ? "current" : "") + "' data-page='" + i + "' " + (i == currentPage ? "disabled" : "") + ">" + i + "</a>"
            }
            $('#pagenumbers').html(pageNumbersHtml);
            //register events for newly loaded buttons
            $('.pagenumber').click(function(e){
                e.preventDefault();
                let pageClicked = this.getAttribute('data-page');
                if (currentPage != pageClicked){
                    $('.pagenumber[data-page=' + currentPage + ']').removeAttr('disabled').prop("disabled", false).removeClass('current');
                    $('.pagenumber[data-page=' + pageClicked + ']').prop("disabled", true).addClass('current');
                    currentPage = pageClicked;
                    loadPage();
                }
                
            })
        }

        function loadPage() {
            let hotelsToShow = hotels.slice((currentPage - 1) * pageSize, currentPage * pageSize);
            var html = tmpl.render(hotelsToShow); 
            $("#fetch-hotel-result").html(html); 
        }

        function calculatePrice() {
            let sum = 0;
            let maxStarInList = Math.max.apply(Math, hotels.map(function(hotel) { return hotel.stars; }))
            let hotelsOfMaxStars = $.grep(hotels, function(hotel, index){
                return (hotel.stars == maxStarInList); 
            });
            for( let i = 0; i < hotelsOfMaxStars.length; i++ ){
                sum += parseFloat(hotelsOfMaxStars[i].price.replace("RM", ""))
            }

            let avg = sum/hotelsOfMaxStars.length;
            let numberOfNights = getNumberOfNights();
            $("#price").text(numberOfNights + ' nights total: ' + (avg * 1 * numberOfNights).toFixed(2));
        }

        function getNumberOfNights(){
            let timeDiff = Math.abs(checkOutDate - checkInDate);
            let numberOfNights = Math.ceil(timeDiff / (1000 * 3600 * 24)) - 1;
            if (numberOfNights > 0) {
                return numberOfNights;
            }
            else {
                return 0;
            }
        }

        function getTodaysDate(){
            let today = new Date();
            let dd = today.getDate();
            let mm = today.getMonth() + 1; //January is 0!
            let yyyy = today.getFullYear();

            if (dd < 10) {
            dd = '0' + dd;
            }

            if (mm < 10) {
            mm = '0' + mm;
            }

            return new Date(yyyy + '-' + mm + '-' + dd);
        }
    </script>
</div>
@endsection