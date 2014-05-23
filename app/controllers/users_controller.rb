class UsersController < ApplicationController
  def index
    @users = User.all()
    render :json => @users
  end

  def show
    @user = User.find(params[:id])
    render :json => @user
  end

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