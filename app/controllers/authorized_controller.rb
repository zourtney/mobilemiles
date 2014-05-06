class AuthorizedController < ApplicationController

  before_action :validate_token

private
  def validate_token
    begin
      token = request.headers['Authorization'].split(' ').last
      JWT.decode(token, 'plaidshirtdays')
    rescue
      render nothing: true, status: :unauthorized
    end
  end

end