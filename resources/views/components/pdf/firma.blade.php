<div class="cols">
    <div>
        <img style="width: 2.5cm"
            src="{{ $firma_installer ? asset('signatures/install/' . $firma_installer) : asset('signatures/no-signature.jpg') }}">
        <p style="padding-left: 1.8cm">Installer Signature</p>
    </div>
    <div>
        <img style="width: 2.5cm"
            src="{{ $firma_foreman ? asset('signatures/empleoye/' . $firma_foreman) : asset('signatures/no-signature.jpg') }}">
        <p style="padding-left: 1.8cm">Superintendent's Signature</p>
    </div>
</div>