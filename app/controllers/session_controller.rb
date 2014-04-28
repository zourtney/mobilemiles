class SessionController < ApplicationController
  def index
    render :json => session
  end
  
  def create
    user = User.find_by_email(params[:email])
    if user && user.authenticate(params[:password])
      session[:user_id] = user.id
      render :json => { user: user, session: session }, status: :created
    else
      render :json => { error: "Invalid email or password" }, status: :unauthorized
    end
  end
  
  def destroy
    session[:user_id] = nil
    render :json => {}, status: :ok
  end
end