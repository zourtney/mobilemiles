class ApplicationController < ActionController::Base


  # https://auth0.com/blog/2014/01/07/angularjs-authentication-with-cookies-vs-token/
  # http://www.intridea.com/blog/2013/11/7/json-web-token-the-useful-little-standard-you-haven-t-heard-about

  
  #
  # TODO: make this work property. It all seems to be ineffective.
  #
  # Send CSRF cookie. See:
  # http://stackoverflow.com/a/15056471/
  # http://stackoverflow.com/a/15761835/
  # https://github.com/nazar/parlmnt/blob/master/app/controllers/application_controller.rb
  # protect_from_forgery
  # after_filter  :set_csrf_cookie_for_ng   #AngularJS protect_from_forgery

protected
  # def json_responder(obj, options = {})
  #   respond_to do |format|
  #     format.json {render({:json => obj}.merge(options))}
  #   end
  # end

  # def set_csrf_cookie_for_ng
  #   cookies['XSRF-TOKEN'] = form_authenticity_token if protect_against_forgery?
  # end

  # def verified_request?
  #   super || form_authenticity_token == request.headers['X_XSRF_TOKEN']
  # end

private
  # def current_user
  #   puts "NOt totally irerlvelent!!!!!!!!"
  #   @current_user ||= User.find(session[:user_id]) if session[:user_id]
  # end
  # helper_method :current_user

  # def authorize
  #   puts "asontihayrchoesnt whateverman"
  #   redirect_to login_url, alert: "Not authorized" if current_user.nil?
  # end

end
