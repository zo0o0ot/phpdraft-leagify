(function(angular, undefined) {
  angular.module("config", [])

.constant("ENV", {
	"apiEndpoint": "http://phpdraft-leagify.rhcloud.com/api/",
	"recaptchaPublicKey": "6LcuYhcTAAAAAKcCHeu75NzrSRSpuV-4PBKevjzF"
})

;
})(angular);
