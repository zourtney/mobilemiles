class UsersController < ApplicationController
  def index
    @users = User.all()
    render :json => @users
  end

  def show
    @user = User.find(params[:id])
    render :json => @user
  end

  # For some reason (I'm giving up for now), the username and password must be
  # sent in through a nested `user` object. So the JSON request should look
  # something like:
  #   {
  #     "authenticity_token": "oeuidhtnrcfyp.o7qjk8iuceohqjkx=",
  #     "user": {
  #       "email": "zourtney@gmail.com",
  #       "password": "aoesunth",
  #       "password_confirmation": "aoeusnth"
  #     }
  #   }
  #
  def create
    @user = User.new(user_params)
    if @user.save
      render :json => @user, status: :created
    else
      render :json => { error: @user.errors }, status: :unprocessable_entity
    end
  end

private
  def user_params
    params.require(:user).permit(:email, :password, :password_confirmation)
  end
end