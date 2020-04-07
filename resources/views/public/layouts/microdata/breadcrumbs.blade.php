<script type="application/ld+json">
{
  "@context": "http://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
  @foreach ($breadcrumbs as $i => $breadcrumb)
      {{ $i>0?',':'' }}
      {
        "@type": "ListItem",
        "position": {{ $i+1 }},
        "name": "{{ $breadcrumb->title }}"@if(!$breadcrumb->last),
        "item": "{{ $breadcrumb->url }}"@endif
      }
  @endforeach
  ]
}
</script>