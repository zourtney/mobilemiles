class ApplicationController < ActionController::Base
  
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
  def current_user
    puts 'I\'ve been searchin\'...searchin\'!!!!'
    @current_user ||= User.find(session[:user_id]) if session[:user_id]
    puts 'You have selected:'
    puts @current_user
  end
  helper_method :current_user

  def authorize
    puts 'Am I helping??????????????????????????????'
    redirect_to login_url, alert: "Not authorized" if current_user.nil?
  end
end
