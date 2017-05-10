<footer class="footer">
    <div class="container">
        <div class="copyright">
            <blockquote class="pull-left">
                <p>{!! Config::get('setting.site_about') !!}</p>
                <p>
                    {!! trans('hifone.powered_by') !!} <span class="pipe">|</span><a href="http://www.miitbeian.gov.cn/">沪ICP备09048395号-17</a>
                    <span>| <script src="https://s4.cnzz.com/z_stat.php?id={{ env('CNZZ_STATICS') }}&web_id={{ env('CNZZ_STATICS') }}" language="JavaScript"></script></span>
                </p>
            </blockquote>
        </div>
        <div class="pull-right hidden-sm hidden-xs">
            <p>
                <a href="/about">关于我们</a>
                <span class="pipe">|</span>
                <a href="/contact">联系我们</a>
                <span class="pipe">|</span>
                <a href="/faq">常见问题解答</a>
            </p>
            <p>
                <a href="http://hifone.com/" target="_blank"><img src="/images/phicomm.png" border="0" height="40" data-toggle="tooltip" data-placement="top" title="Powered by Phicomm" /></a>
            </p>
        </div>
    </div>
</footer>