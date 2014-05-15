class AuthorizedController < ApplicationController

  before_action :validate_token

private
  def validate_token
    begin
      token = request.headers['Authorization'].split(' ').last
      payload, header = JWT.decode(token, 'plaidshirtdays')

      # https://github.com/vline/vline-rails/blob/master/lib/vline.rb
      @user = User.find_by(id: payload['user_id'], email: payload['email'])
      if not @user
        render nothing: true, status: :unauthorized
      end

      #TODO: check token expiration

      # puts user
      # puts payload['user_id']
      # puts payload['email']
      # puts session[:user_id]
      # puts header
      #user = User.find_by_email(payload[9][:user_id])
    rescue
      render nothing: true, status: :unauthorized
    end
  end

end