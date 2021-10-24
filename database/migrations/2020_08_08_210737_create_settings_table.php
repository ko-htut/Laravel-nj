<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->String('app_name');
            $table->String('authorization')->nullable();
            $table->String('tmdb_api_key')->nullable();
            $table->String('purchase_key')->nullable();
            $table->longText('tmdb_lang');
            $table->String('app_url_android')->nullable();
            $table->boolean('autosubstitles');
            $table->boolean('livetv');
            $table->boolean('ads_player');
            $table->boolean('anime');
            $table->integer('facebook_show_interstitial');
            $table->integer('ad_show_interstitial');
            $table->boolean('ad_interstitial');
            $table->String('ad_unit_id_interstitial')->nullable();
            $table->boolean('ad_banner');
            $table->String('ad_unit_id_banner')->nullable();
            $table->boolean('ad_face_audience_interstitial');
            $table->boolean('ad_face_audience_banner');
            $table->String('ad_unit_id_facebook_interstitial_audience')->nullable();
            $table->String('ad_unit_id_facebook_banner_audience')->nullable();
            $table->longText('privacy_policy')->nullable();
            $table->String('latestVersion')->nullable();
            $table->String('update_title')->nullable();
            $table->longText('releaseNotes')->nullable();
            $table->integer('enable_custom_message');
            $table->longText('custom_message')->nullable();
            $table->String('url')->nullable();
            $table->String('imdb_cover_path')->nullable();
            $table->String('paypal_client_id')->nullable();
            $table->String('paypal_amount')->nullable();
            $table->String('stripe_publishable_key')->nullable();
            $table->String('stripe_secret_key')->nullable();
            $table->integer('featured_home_numbers');
            $table->String('startapp_id')->nullable();
            $table->String('ad_unit_id_rewarded')->nullable();
            $table->String('ad_unit_id__facebook_rewarded')->nullable();
            $table->String('ad_unit_id__appodeal_rewarded')->nullable();
            $table->String('unity_game_id')->nullable();
            $table->String('default_network')->nullable();
            $table->integer('wach_ads_to_unlock');
            $table->boolean('aws_s3_storage')->default(0);
            $table->String('aws_access_key_id')->nullable();
            $table->String('aws_secret_access_key')->nullable();
            $table->String('aws_default_region')->nullable();
            $table->String('aws_bucket')->nullable();
            $table->boolean('wasabi_storage')->default(0);
            $table->String('wasabi_access_key_id')->nullable();
            $table->String('wasabi_secret_access_key')->nullable();
            $table->String('wasabi_default_region')->nullable();
            $table->String('wasabi_bucket')->nullable();
            $table->String('default_media_placeholder_path')->nullable();
            $table->integer('next_episode_timer');
            $table->String('facebook_url')->nullable();
            $table->String('twitter_url')->nullable();
            $table->String('instagram_url')->nullable();
            $table->String('telegram_url')->nullable();
            $table->String('ad_unit_id_native')->nullable();
            $table->String('default_payment')->nullable();
            $table->String('paypal_currency')->nullable();
            $table->integer('appodeal_show_interstitial');
            $table->integer('ad_unit_id_native_enable');
            $table->integer('appodeal_banner');
            $table->integer('appodeal_interstitial');
            $table->integer('server_dialog_selection');
            $table->integer('download_premuim_only')->default(0);
            $table->String('default_network_player')->nullable();
            $table->integer('wach_ads_to_unlock_player')->default(0);
            $table->integer('enable_custom_banner')->default(0);
            $table->String('custom_banner_image')->nullable();
            $table->String('custom_banner_image_link')->nullable();
            $table->String('default_downloads_options')->nullable();
            $table->text('mantenance_mode_message')->nullable();
            $table->String('splash_image')->nullable();
            $table->String('default_youtube_quality')->nullable();
            $table->integer('mantenance_mode')->default(0);
            $table->integer('allow_adm')->default(0);
            $table->integer('enable_previews')->default(0);
            $table->integer('enable_pinned')->default(0);
            $table->integer('startapp_banner')->default(0);
            $table->integer('startapp_interstitial')->default(0);
            $table->integer('enable_vlc')->default(0);
            $table->integer('resume_offline')->default(1);
            $table->String('user_agent')->nullable();
            $table->integer('unityads_banner')->default(0);
            $table->integer('unityads_interstitial')->default(0);
            $table->integer('streaming')->default(1);
            $table->integer('enable_banner_bottom')->default(0);
            $table->integer('ad_face_audience_native')->default(0);
            $table->integer('enable_upcoming')->default(1);
            $table->String('ad_unit_id_facebook_native_audience')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
