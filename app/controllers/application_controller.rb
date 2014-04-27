class ApplicationController < ActionController::Base
  # Prevent CSRF attacks by raising an exception.
  # For APIs, you may want to use :null_session instead.
  protect_from_forgery with: :exception

  # If you get lazy with the AJAXes:
  # protect_from_forgery
  # skip_before_action :verify_authenticity_token, if: :json_request?

# protected
#   def json_request?
#     request.format.json?
#   end

private
  def current_user
    @current_user ||= User.find(session[:user_id]) if session[:user_id]
  end
  helper_method :current_user

  def authorize
    redirect_to login_url, alert: "Not authorized" if current_user.nil?
  end
end
